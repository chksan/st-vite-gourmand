<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderStat;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'menu_id'          => 'required|exists:menus,id',
            'nb_personnes'     => 'required|integer|min:2',
            'delivery_address' => 'required|string|min:10',
            'delivery_date'    => 'required|date|after_or_equal:today',
            'delivery_time'    => 'required',
        ]);

        $menu = Menu::findOrFail($request->menu_id);

        if ($menu->stock <= 0) {
            return response()->json([
                'message' => 'Désolé, ce menu n\'est plus disponible en stock.'
            ], 422);
        }

        if ($request->nb_personnes < $menu->min_personnes) {
            return response()->json([
                'message' => "Ce menu nécessite un minimum de {$menu->min_personnes} personnes."
            ], 422);
        }

        // Calculate delivery fee
        try {
            $deliveryFee = $this->calculateDeliveryFee($request->delivery_address);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        // Price calculation
        $basePrice = $menu->price;
        $total = $basePrice * ($request->nb_personnes / $menu->min_personnes);

        if ($request->nb_personnes >= $menu->min_personnes + 5) {
            $total *= 0.9; // 10% discount
        }

        $total += $deliveryFee;

        $order = Order::create([
            'user_id'          => Auth::id(),
            'menu_id'          => $menu->id,
            'nb_personnes'     => $request->nb_personnes,
            'total_price'      => round($total, 2),
            'delivery_address' => $request->delivery_address,
            'delivery_date'    => $request->delivery_date,
            'delivery_time'    => $request->delivery_time,
            'delivery_fee'     => round($deliveryFee, 2),
            'status'           => 'pending',
        ]);

        $menu->decrement('stock');

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status'   => 'pending',
            'comment'  => 'Commande créée',
        ]);

        Mail::to($order->user->email)->send(new OrderConfirmation($order));

        return response()->json([
            'order'   => $order->fresh('menu'),
            'message' => 'Commande enregistrée avec succès !'
        ], 201);
    }

    /**
     * Calculate delivery fee using Nominatim (OpenStreetMap)
     * Throws an exception if address cannot be found
     */
    private function calculateDeliveryFee(string $address): float
    {
        $baseFee = 5.00;

        // If address contains "Bordeaux" → base fee only
        if (stripos($address, 'bordeaux') !== false) {
            return $baseFee;
        }

        // Call Nominatim API to get coordinates
        $response = Http::withHeaders([
            'User-Agent' => 'ViteGourmand-ECF/1.0'
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $address,
            'format' => 'json',
            'limit' => 1
        ]);

        if (!$response->successful()) {
            throw new \Exception("Une erreur s'est produite lors de la vérification de votre adresse.");
        }

        $data = $response->json();

        // If no result returned → invalid address
        if (count($data) === 0) {
            throw new \Exception("L'adresse de livraison n'a pas pu être validée. Veuillez saisir une adresse valide.");
        }

        $lat = (float)$data[0]['lat'];
        $lon = (float)$data[0]['lon'];

        $bordeauxLat = 44.8378;
        $bordeauxLon = -0.5792;

        $distanceKm = $this->haversineGreatCircleDistance($bordeauxLat, $bordeauxLon, $lat, $lon);

        return $baseFee + (0.59 * $distanceKm);
    }

    /**
     * Haversine formula - calculate distance in km
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo): float
    {
        $earthRadius = 6371;

        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    public function index(Request $request)
    {
        $query = Order::with(['menu:id,title,min_personnes,price', 'review:id,order_id,rating,comment,is_validated'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(5);

        // No need to provide extra informations such as :
        $orders->getCollection()->transform(function ($order) {
            $order->makeHidden([
                'cancelled_by',
                'contact_mode',
                'cancel_reason',
            ]);
            return $order;
        });

        return response()->json($orders);
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Seules les commandes en attente peuvent être annulées par le client.'
            ], 400);
        }

        $menu = $order->menu;

        //stock update
        if ($menu) {
            $menu->increment('stock');
        }

        $order->update(['status' => 'cancelled']);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status'   => 'cancelled',
            'comment'  => 'Commande annulée par le client',
        ]);

        return response()->json([
            'message' => 'Commande annulée avec succès. Le stock a été restitué.'
        ]);
    }

    public function tracking(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status === 'pending') {
            return response()->json(['message' => 'Suivi non disponible avant acceptation'], 403);
        }

        $history = OrderStatusHistory::where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }

    public function update(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Impossible de modifier une commande déjà acceptée'], 400);
        }

        $request->validate([
            'nb_personnes' => 'required|integer|min:' . $order->menu->min_personnes,
            'delivery_date' => 'required|date|after_or_equal:today',
            'delivery_time' => 'required',
            'delivery_address' => 'required|string|min:10',
        ]);

        $menu = $order->menu;

        // Recalculate delivery fee if address changed
        try {
            $deliveryFee = $request->delivery_address !== $order->delivery_address
                ? $this->calculateDeliveryFee($request->delivery_address)
                : $order->delivery_fee;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $pricePerPerson = $menu->price / $menu->min_personnes;
        $total = $pricePerPerson * $request->nb_personnes;

        if ($request->nb_personnes >= $menu->min_personnes + 5) {
            $total *= 0.9;
        }

        $total += $deliveryFee;

        $order->update([
            'nb_personnes' => $request->nb_personnes,
            'delivery_date' => $request->delivery_date,
            'delivery_time' => $request->delivery_time,
            'delivery_address' => $request->delivery_address,
            'delivery_fee' => round($deliveryFee, 2),
            'total_price' => round($total, 2),
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'comment' => 'Commande modifiée par le client',
        ]);

        return response()->json(['order' => $order->fresh('menu'), 'message' => 'Commande mise à jour']);
    }

    public function storeReview(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'completed') {
            return response()->json(['message' => 'Vous ne pouvez noter qu\'une commande terminée'], 400);
        }

        if ($order->review) {
            return response()->json(['message' => 'Vous avez déjà laissé un avis'], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $review = \App\Models\Review::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_validated' => null, // en attente de validation employé
        ]);

        return response()->json(['review' => $review, 'message' => 'Avis soumis, en attente de validation'], 201);
    }
}
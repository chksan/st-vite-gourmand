<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            'delivery_date' => 'required|date|after_or_equal:today',
            'delivery_time'    => 'required',
        ]);

        $menu = Menu::findOrFail($request->menu_id);

        // Calculate delivery fee using Nominatim API
        $deliveryFee = $this->calculateDeliveryFee($request->delivery_address);

        // Base price calculation
        $basePrice = $menu->price;
        $total = $basePrice * ($request->nb_personnes / $menu->min_personnes);

        // 10% discount if 5+ persons above minimum
        if ($request->nb_personnes >= $menu->min_personnes + 5) {
            $total *= 0.9;
        }

        $total += $deliveryFee;

        $order = Order::create([
            'user_id'          => Auth::id(),
            'menu_id'          => $request->menu_id,
            'nb_personnes'     => $request->nb_personnes,
            'total_price'      => round($total, 2),
            'delivery_address' => $request->delivery_address,
            'delivery_date'    => $request->delivery_date,
            'delivery_time'    => $request->delivery_time,
            'delivery_fee'     => round($deliveryFee, 2),
            'status'           => 'pending',
        ]);



        //Implement paypal sandbox in the future?

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status'   => 'pending',
            'comment'  => 'Order created',
        ]);

        return response()->json([
            'order'   => $order,
            'message' => 'Order placed successfully'
        ], 201);
    }

    /**
     * Calculate delivery fee using Nominatim (OpenStreetMap)
     */
    private function calculateDeliveryFee(string $address): float
    {
        $baseFee = 5.00;

        // If address is in Bordeaux → base fee only
        if (stripos($address, 'bordeaux') !== false) {
            return $baseFee;
        }

        // Call Nominatim API to get coordinates
        $response = Http::withHeaders([
            'User-Agent' => 'ViteGourmand-ECF/1.0'
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q'      => $address,
            'format' => 'json',
            'limit'  => 1
        ]);

        if ($response->successful() && count($response->json()) > 0) {
            $data = $response->json()[0];
            $lat = (float)$data['lat'];
            $lon = (float)$data['lon'];

            // Bordeaux coordinates
            $bordeauxLat = 44.8378;
            $bordeauxLon = -0.5792;

            $distanceKm = $this->haversineGreatCircleDistance($bordeauxLat, $bordeauxLon, $lat, $lon);

            return $baseFee + (0.59 * $distanceKm);
        }

        // Fallback if API fails
        return $baseFee + (0.59 * 15);
    }

    /**
     * Haversine formula - calculate distance in km
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo): float
    {
        $earthRadius = 6371;

        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo   = deg2rad($latitudeTo);
        $lonTo   = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }


    /**
     * Get all user order
     */
    public function index()
    {
        $orders = Order::with('menu')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Cancel an order
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Seules les commandes en attente peuvent être annulées'
            ], 400);
        }

        $order->update(['status' => 'cancelled']);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status'   => 'cancelled',
            'comment'  => 'Commande annulée par le client',
        ]);

        return response()->json([
            'message' => 'Commande annulée avec succès'
        ]);
    }

}
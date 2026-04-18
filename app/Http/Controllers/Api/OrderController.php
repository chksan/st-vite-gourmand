<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'menu_id'          => 'required|exists:menus,id',
            'nb_personnes'     => 'required|integer|min:2',
            'delivery_address' => 'required|string',
            'delivery_date'    => 'required|date',
            'delivery_time'    => 'required',
            'distance_km'      => 'nullable|numeric|min:0',
        ]);

        $menu = \App\Models\Menu::findOrFail($request->menu_id);


        // 0,59€ / KM
        $baseFee = 5.00;
        $distanceKm = $request->distance_km ?? 0;

        // Inside bordeaux = No extra fees.
        $isInBordeaux = stripos($request->delivery_address, 'bordeaux') !== false;

        $deliveryFee = $isInBordeaux
            ? $baseFee
            : $baseFee + (0.59 * $distanceKm);

        // Base price calculation
        $basePrice = $menu->price;
        $total = $basePrice * ($request->nb_personnes / $menu->min_personnes);

        // 10% discount if +5 persons above minimum
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

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status'   => 'pending',
            'comment'  => 'Commande créée',
        ]);

        return response()->json([
            'order'   => $order,
            'message' => 'Commande enregistrée avec succès'
        ], 201);
    }}
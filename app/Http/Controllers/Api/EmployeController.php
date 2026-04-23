<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Menu;
use App\Models\Plat;
use App\Http\Controllers\Controller;
use App\Models\Horaire;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeController extends Controller
{
    public function orders(Request $request)
    {
        $query = Order::with(['menu', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%');
            });
        }

        $orders = $query->paginate(10);

        return response()->json($orders);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:accepted,preparing,delivering,delivered,waiting_material,completed,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status'   => $request->status,
            'comment'  => 'Statut mis à jour par employé',
        ]);

        return response()->json(['message' => 'Statut mis à jour']);
    }

    public function menus()
    {
        return response()->json(Menu::all());
    }

    public function plats()
    {
        return response()->json(Plat::all());
    }

    public function horaires()
    {
        return response()->json(Horaire::all());
    }

    public function reviews()
    {
        $reviews = Review::with(['user', 'order.menu'])
            ->where('is_validated', false)
            ->get();

        return response()->json($reviews);
    }

    public function validateReview(Review $review, Request $request)
    {
        $review->update([
            'is_validated' => true,
            'validated_by' => Auth::id()
        ]);

        return response()->json(['message' => 'Avis validé']);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\Horaires as Horaire;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Plat;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeController extends Controller
{
    private function formatOrder(Order $order)
    {
        return [
            ...$order->toArray(),
            'date_prestation' => $order->delivery_date,
            'heure_livraison' => $order->delivery_time,
            'adresse_livraison' => $order->delivery_address,
            'prix_menu' => $order->total_price,
            'prix_livraison' => $order->delivery_fee,

            'user' => $order->user ? [
                ...$order->user->toArray(),
                'gsm' => $order->user->phone,
            ] : null,
        ];
    }

    public function orders(Request $request)
    {
        $query = Order::with(['menu', 'user'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%')
                    ->orWhere('email', 'like', '%' . $request->client . '%');
            });
        }

        return response()->json(
            $query->paginate(10)->through(fn ($order) => $this->formatOrder($order))
        );
    }

    public function showOrder(Order $order)
    {
        $order->load(['menu.plats.allergens', 'user', 'statusHistory']);

        return response()->json($this->formatOrder($order));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,accepted,preparing,delivering,delivered,waiting_material,completed,cancelled',
            'contact_mode' => 'required_if:status,cancelled|nullable|in:gsm,email',
            'cancel_reason' => 'required_if:status,cancelled|nullable|string|max:1000',
        ]);

        $order->update([
            'status' => $data['status'],
        ]);

        $comment = 'Statut mis à jour par employé';

        if ($data['status'] === 'cancelled') {
            $comment = 'Annulation via ' . $data['contact_mode'] . ' : ' . $data['cancel_reason'];
        }

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $data['status'],
            'comment' => $comment,
        ]);

        return response()->json(['message' => 'Statut mis à jour']);
    }

    public function menus()
    {
        return response()->json(
            Menu::with('plats.allergens')->orderByDesc('created_at')->get()
        );
    }

    public function storeMenu(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'theme' => 'required|string|max:50',
            'regime' => 'required|string|max:50',
            'min_personnes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'conditions' => 'nullable|string',
            'plat_ids' => 'nullable|array',
            'plat_ids.*' => 'exists:plats,id',
        ]);

        $platIds = $data['plat_ids'] ?? [];
        unset($data['plat_ids']);

        $menu = Menu::create($data);
        $menu->plats()->sync($platIds);

        return response()->json($menu->load('plats.allergens'), 201);
    }

    public function updateMenu(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'theme' => 'required|string|max:50',
            'regime' => 'required|string|max:50',
            'min_personnes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'conditions' => 'nullable|string',
            'plat_ids' => 'nullable|array',
            'plat_ids.*' => 'exists:plats,id',
        ]);

        $platIds = $data['plat_ids'] ?? [];
        unset($data['plat_ids']);

        $menu->update($data);
        $menu->plats()->sync($platIds);

        return response()->json($menu->load('plats.allergens'));
    }

    public function deleteMenu(Menu $menu)
    {
        $menu->plats()->detach();
        $menu->delete();

        return response()->json(['message' => 'Menu supprimé']);
    }

    public function plats()
    {
        return response()->json(
            Plat::with('allergens')->orderBy('type')->orderBy('title')->get()
        );
    }

    public function storePlat(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:entree,plat,dessert',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'allergen_ids' => 'nullable|array',
            'allergen_ids.*' => 'exists:allergens,id',
        ]);

        $allergenIds = $data['allergen_ids'] ?? [];
        unset($data['allergen_ids']);

        $plat = Plat::create($data);
        $plat->allergens()->sync($allergenIds);

        return response()->json($plat->load('allergens'), 201);
    }

    public function updatePlat(Request $request, Plat $plat)
    {
        $data = $request->validate([
            'type' => 'required|in:entree,plat,dessert',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'allergen_ids' => 'nullable|array',
            'allergen_ids.*' => 'exists:allergens,id',
        ]);

        $allergenIds = $data['allergen_ids'] ?? [];
        unset($data['allergen_ids']);

        $plat->update($data);
        $plat->allergens()->sync($allergenIds);

        return response()->json($plat->load('allergens'));
    }

    public function deletePlat(Plat $plat)
    {
        $plat->allergens()->detach();
        $plat->menus()->detach();
        $plat->delete();

        return response()->json(['message' => 'Plat supprimé']);
    }

    public function allergens()
    {
        return response()->json(
            Allergen::orderBy('name')->get()
        );
    }

    public function storeAllergen(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:allergens,name',
        ]);

        return response()->json(Allergen::create($data), 201);
    }

    public function deleteAllergen(Allergen $allergen)
    {
        $allergen->plats()->detach();
        $allergen->delete();

        return response()->json(['message' => 'Allergène supprimé']);
    }

    public function horaires()
    {
        return response()->json(
            Horaire::all()->map(function ($h) {
                $h->opening_time = substr($h->opening_time, 0, 5);
                $h->closing_time = substr($h->closing_time, 0, 5);
                $h->is_closed = (bool) $h->is_closed;
                return $h;
            })
        );
    }

    public function updateHoraire(Request $request, Horaire $horaire)
    {
        $data = $request->validate([
            'opening_time' => 'nullable',
            'closing_time' => 'nullable',
            'is_closed' => 'required|boolean',
        ]);

        if (!$data['is_closed']) {
            if (empty($data['opening_time']) || empty($data['closing_time'])) {
                return response()->json([
                    'message' => 'Les heures d’ouverture et de fermeture sont obligatoires.',
                ], 422);
            }

            $opening = substr($data['opening_time'], 0, 5);
            $closing = substr($data['closing_time'], 0, 5);

            if ($closing <= $opening) {
                return response()->json([
                    'message' => 'L’heure de fermeture doit être après l’heure d’ouverture.',
                ], 422);
            }

            $data['opening_time'] = $opening;
            $data['closing_time'] = $closing;
        }

        $horaire->update($data);

        return response()->json($horaire);
    }

    public function reviews()
    {
        return response()->json(
            Review::with(['user', 'order.menu'])
                ->where('is_validated', false)
                ->orderByDesc('created_at')
                ->get()
        );
    }

    public function validateReview(Review $review)
    {
        $review->update([
            'is_validated' => true,
            'validated_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Avis validé']);
    }

    public function rejectReview(Review $review)
    {
        $review->delete();

        return response()->json(['message' => 'Avis refusé']);
    }
}
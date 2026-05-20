<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EmployeAccountCreated;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{

    /**
     * List all employees
     */
    public function employees()
    {
        $employees = User::where('role', 'employe')
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'email', 'is_active', 'created_at']);

        return response()->json($employees);
    }

    //password sent manually by admin
    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:10',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
        ]);

        $employee = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'employe',
            'is_active' => true,
        ]);

        Mail::to($employee->email)->send(new EmployeAccountCreated($employee));

        return response()->json([
            'employee' => $employee->only(['id', 'name', 'email', 'is_active', 'created_at']),
            'message'  => 'Compte employé créé. Un email a été envoyé à ' . $employee->email,
        ], 201);
    }

    public function toggleEmployee(User $user)
    {
        // cannot disable an admin account
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Impossible de désactiver un compte administrateur'], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activé' : 'désactivé';

        return response()->json([
            'employee' => $user->only(['id', 'name', 'email', 'is_active']),
            'message'  => "Compte {$status} avec succès",
        ]);
    }


    public function deleteEmployee(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Impossible de supprimer un compte administrateur'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Compte supprimé']);
    }
    public function ordersPerMenu(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        $query = Order::with('menu:id,title')
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('menu_id, COUNT(*) as total_orders')
            ->groupBy('menu_id');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $stats = $query->get()->map(fn ($row) => [
            'menu_id'      => $row->menu_id,
            'menu_title'   => $row->menu?->title ?? 'Menu supprimé',
            'total_orders' => $row->total_orders,
        ]);

        return response()->json($stats);
    }

    public function revenuePerMenu(Request $request)
    {
        $request->validate([
            'menu_id' => 'nullable|exists:menus,id',
            'from'    => 'nullable|date',
            'to'      => 'nullable|date|after_or_equal:from',
        ]);

        $query = Order::with('menu:id,title')
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('menu_id, COUNT(*) as total_orders, SUM(total_price) as total_revenue')
            ->groupBy('menu_id');

        if ($request->filled('menu_id')) {
            $query->where('menu_id', $request->menu_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $stats = $query->get()->map(fn ($row) => [
            'menu_id'       => $row->menu_id,
            'menu_title'    => $row->menu?->title ?? 'Menu supprimé',
            'total_orders'  => $row->total_orders,
            'total_revenue' => round((float) $row->total_revenue, 2),
        ]);

        return response()->json($stats);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::query();

        $query->when($request->filled('max_price') && is_numeric($request->max_price), function ($q) use ($request) {
            $q->where('price', '<=', $request->max_price);
        });

        $query->when($request->filled('theme'), function ($q) use ($request) {
            $q->where('theme', $request->theme);
        });

        $query->when($request->filled('regime'), function ($q) use ($request) {
            $q->where('regime', $request->regime);
        });

        $query->when($request->filled('min_personnes') && is_numeric($request->min_personnes), function ($q) use ($request) {
            $q->where('min_personnes', '>=', $request->min_personnes);
        });

        $menus = $query->get();

        return response()->json($menus);
    }
    public function show($id){
        dd(Menu::query()->get());
        $menu = Menu::with('plats')->findOrFail($id);
        return response()->json($menu);
    }
}

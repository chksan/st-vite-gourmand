<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Horaires;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getHomeData()
    {
        $horaires = Horaires::all();

        $reviews = Review::with('user:id,name')
            ->where('is_validated', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $cleanReviews = $reviews->map(function ($review) {
            $displayName = 'Client';

            if ($review->user && $review->user->name) {
                $nameParts = explode(' ', trim($review->user->name));
                $displayName = $nameParts[0];
            }

            return [
                'id'         => $review->id,
                'rating'     => $review->rating,
                'comment'    => $review->comment,
                'created_at' => $review->created_at,
                'user'       => [
                    'display_name' => $displayName
                ]
            ];
        });

        return response()->json([
            'horaires' => $horaires,
            'reviews'  => $cleanReviews
        ]);
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\Http\Request;

class CafeTopController extends Controller
{
    public function top10()
    {
        // ดึง 10 อันดับ โดยเรียงจากเรตติ้ง > ไลก์ > จำนวนรีวิว
        $topRatedCafes = Cafe::query()
            ->select('id', 'cafe_name', 'image_path', 'rating_avg', 'likes_count', 'review_count')
            ->when(\Schema::hasColumn('cafes', 'is_top10'), fn($q) => $q->where('is_top10', true))
            ->orderByDesc('rating_avg')
            ->orderByDesc('likes_count')
            ->orderByDesc('review_count')
            ->take(10)
            ->get();

        return view('cafes.top10', compact('topRatedCafes'));
    }
}

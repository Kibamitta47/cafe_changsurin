<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserReviewController extends Controller
{
    /**
     * Display a listing of the user's reviews.
     */
    public function index()
    {
        $reviews = auth()->user()
            ->reviews()
            ->with('cafe')
            ->latest()
            ->paginate(10);

        return view('user.reviews.my', compact('reviews'));
    }

    /**
     * Show the form for editing the specified review.
     */
   public function edit(Review $review)
{
    if ($review->user_id !== auth()->id()) {
        abort(403, 'UNAUTHORIZED ACTION.');
    }

    // โหลดความสัมพันธ์ cafe
    $review->load('cafe');

    return view('user.reviews.edit', compact('review'));
}

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== auth()->id()) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('images')) {
            if (!empty($review->images)) {
                foreach ($review->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('reviews', 'public');
            }
        } else {
            $paths = $review->images ?? [];
        }

        $review->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
            'images' => $paths,
        ]);

        return redirect()->back()->with('success', 'แก้ไขรีวิวเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        if (!empty($review->images)) {
            foreach ($review->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $review->delete();

        return redirect()->route('user.reviews.my')->with('success', 'ลบรีวิวเรียบร้อยแล้ว!');
    }
}

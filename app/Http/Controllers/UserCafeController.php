<?php
// app/Http/Controllers/UserCafeController.php
namespace App\Http\Controllers;

use App\Models\Cafe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class UserCafeController extends Controller
{
    public function create(): View
    {
        return view('user.cafes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cafe_name' => 'required|string|max:255',
            'price_range' => 'nullable|string|max:255',
            'place_name' => 'required|string|max:255',
            'address' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'facebook_page' => 'nullable|string|max:2048',
            'instagram_page' => 'nullable|string|max:2048',
            'line_id' => 'nullable|string|max:255',
            'open_day' => 'nullable|string|max:255',
            'close_day' => 'nullable|string|max:255',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'payment_methods' => 'nullable|array',
            'facilities' => 'nullable|array',
            'other_services' => 'nullable|array',
            'facilities.*' => 'string',
            'payment_methods.*' => 'string',
            'other_services.*' => 'string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'cafe_styles' => 'nullable|array',
            'cafe_styles.*' => 'string',
            'other_style' => 'nullable|string|max:255',
            'parking' => 'nullable|boolean',
            'credit_card' => 'nullable|boolean',
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('cafes', 'public');
            }
        }
        $data['images'] = $imagePaths;
        $data['payment_methods'] = $request->input('payment_methods', []);
        $data['facilities']      = $request->input('facilities', []);
        $data['other_services']  = $request->input('other_services', []);
        $data['cafe_styles']     = $request->input('cafe_styles', []);

        Cafe::create($data);

        return redirect()->route('user.cafes.my')->with('success', 'ส่งข้อมูลคาเฟ่ให้ผู้ดูแลระบบตรวจสอบแล้ว');
    }

    public function myCafes(): View
    {
        $cafes = Auth::user()->cafes()->withCount('likers')->latest()->paginate(9);
        return view('user.cafes.my', compact('cafes'));
    }

    public function myLikedCafes(): View
    {
        $likedCafes = Auth::user()->likedCafes()
            ->orderBy('cafe_likes.created_at', 'desc')
            ->paginate(12);

        return view('user.liked-cafes', compact('likedCafes'));
    }

    public function edit(Cafe $cafe): View
    {
        abort_if(Auth::id() !== $cafe->user_id, 403);
        return view('user.editcafe', compact('cafe'));
    }

    public function update(Request $request, Cafe $cafe)
    {
        abort_if(Auth::id() !== $cafe->user_id, 403);

        $data = $request->validate([
            'cafe_name' => 'required|string|max:255',
            'price_range' => 'nullable|string|max:255',
            'place_name' => 'required|string|max:255',
            'address' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'facebook_page' => 'nullable|string|max:2048',
            'instagram_page' => 'nullable|string|max:2048',
            'line_id' => 'nullable|string|max:255',
            'open_day' => 'nullable|string|max:255',
            'close_day' => 'nullable|string|max:255',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'payment_methods' => 'nullable|array',
            'facilities' => 'nullable|array',
            'other_services' => 'nullable|array',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'cafe_styles' => 'nullable|array',
            'cafe_styles.*' => 'string',
            'other_style' => 'nullable|string|max:255',
            'parking' => 'nullable|boolean',
            'credit_card' => 'nullable|boolean',
        ]);

        $newImagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $newImagePaths[] = $image->store('cafes', 'public');
            }
        }

        $data['images'] = array_merge($request->input('existing_images', []), $newImagePaths);
        $data['payment_methods'] = $request->input('payment_methods', []);
        $data['facilities']      = $request->input('facilities', []);
        $data['other_services']  = $request->input('other_services', []);
        $data['cafe_styles']     = $request->input('cafe_styles', []);

        $cafe->update($data);

        return redirect()->route('user.cafes.my')->with('success', 'ข้อมูลคาเฟ่อัปเดตแล้ว');
    }

    public function destroy(Cafe $cafe)
    {
        abort_if(Auth::id() !== $cafe->user_id, 403);

        if (is_array($cafe->images)) {
            foreach ($cafe->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $cafe->delete();
        return redirect()->route('user.cafes.my')->with('success', 'ลบคาเฟ่เรียบร้อย');
    }

    public function toggleLike(Request $request, Cafe $cafe): JsonResponse
    {
        $user = Auth::user();

        // ป้องกันซ้ำด้วย unique index (ดู migration ด้านล่าง)
        $user->likedCafes()->toggle($cafe->cafe_id);

        $isLiked = $user->likedCafes()
            ->where('cafe_likes.cafe_id', $cafe->cafe_id)
            ->exists();

        return response()->json(['status' => 'success','is_liked' => $isLiked]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminID;
use App\Models\AddnewsAdmin; // << ต้องอยู่ข้างบน ไม่ซ้ำ

class AuthController extends Controller
{
    public function showRegister() {
        return view('admin.register');
    }

    public function register(Request $request) {
        $request->validate([
            'UserName' => 'required|unique:admin_id',
            'Email' => 'required|email|unique:admin_id',
            'Password' => 'required|min:6',
        ]);

        AdminID::create([
            'UserName' => $request->UserName,
            'Email' => $request->Email,
            'Password' => bcrypt($request->Password),
        ]);

        return redirect()->route('login');
    }

    public function showLogin() {
        return view('admin.login');
    }

    public function login(Request $request) {
        $admin = AdminID::where('UserName', $request->UserName)->first();

        if ($admin && Hash::check($request->Password, $admin->Password)) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('home');
        }

        return back()->withErrors(['login_error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('login');
    }

    public function home() {
        return view('admin.home');
    }

    public function showIncreaseForm(){
        return view('admin.increase-admin');
    }

public function addNews()
{
    $news = AddnewsAdmin::latest()->get();
    return view('admin.Addnews-admin', compact('news'));
}

    public function editProfile() {
        return view('admin.edit-profileadmin');
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $admin->UserName = $request->input('name');
        $admin->Email = $request->input('email');

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('profile_images', $imageName, 'public');
            $admin->profile_image = $imageName;
        }

        $admin->save();

        return redirect()->back()->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }

    // ✅ ฟังก์ชันเดียวพอ: สำหรับบันทึกข่าวสาร
    public function storeNews(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('news_images', 'public');
            }
        }

        AddnewsAdmin::create([
            'title' => $request->title,
            'content' => $request->content,
            'images' => json_encode($imagePaths),
        ]);

        return redirect('/addnews-admin')->with('success', 'เพิ่มข่าวสารเรียบร้อยแล้ว');
    }

    public function editNews($id)
{
    $news = AddnewsAdmin::findOrFail($id);
    return view('admin.edit-news', compact('news'));
}

public function deleteNews($id)
{
    $news = AddnewsAdmin::findOrFail($id);

    // ลบรูปภาพ
    if ($news->images) {
        foreach (json_decode($news->images) as $img) {
            \Storage::disk('public')->delete($img);
        }
    }

    $news->delete();
    return redirect()->back()->with('success', 'ลบข่าวสารเรียบร้อยแล้ว');
}

public function updateNews(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $news = AddnewsAdmin::findOrFail($id);

    $news->title = $request->title;
    $news->content = $request->content;

    if ($request->hasFile('images')) {
        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('news_images', 'public');
            $imagePaths[] = $path;
        }
        $news->images = json_encode($imagePaths);
    }

    $news->save();

   return redirect('/addnews-admin#newsSection')->with('success', 'อัปเดตข่าวสารเรียบร้อยแล้ว');
}

}

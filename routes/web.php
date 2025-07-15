<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AdminCafeController;
use App\Http\Controllers\UserCafeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AddnewsAdminController;
use App\Http\Controllers\NewsController;

/*
|--------------------------------------------------------------------------
| User Protected Routes (สำหรับ User ที่ Login แล้ว)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->name('user.')->group(function () {
    Route::get('/dashboard', fn() => view('user.dashboard'))->name('dashboard');
    Route::get('/profile', fn() => view('user.profile'))->name('profile.show');
    Route::put('/profile', [UserAuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

    // Route สำหรับการจัดการ Like/Unlike
    // ชื่อ Route เต็มคือ 'user.cafes.toggleLike'
    Route::post('/cafes/{cafe}/toggle-like', [UserCafeController::class, 'toggleLike'])->name('cafes.toggleLike');
    
    // Route สำหรับแสดงหน้าคาเฟ่ที่ถูกใจ
    // ชื่อ Route เต็มคือ 'user.cafes.myLiked'
    Route::get('/my-liked-cafes', [UserCafeController::class, 'myLikedCafes'])->name('cafes.myLiked');


    Route::prefix('cafes')->group(function () {
        Route::get('/create', [UserCafeController::class, 'create'])->name('cafes.create');
        Route::post('/', [UserCafeController::class, 'store'])->name('cafes.store');
        Route::get('/my-cafes', [UserCafeController::class, 'myCafes'])->name('cafes.my');
        Route::get('/my-cafes/{cafe}/edit', [UserCafeController::class, 'edit'])->name('cafes.edit');
        Route::put('/my-cafes/{cafe}', [UserCafeController::class, 'update'])->name('cafes.update');
        Route::delete('/my-cafes/{cafe}', [UserCafeController::class, 'destroy'])->name('cafes.destroy');
    });

    // Reviews
    Route::get('/cafes/{cafe_id}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

/*
|--------------------------------------------------------------------------
| Public Routes (เข้าถึงได้ทุกคน ไม่ต้อง Login)
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [AdminCafeController::class, 'welcome'])->name('welcome');

// Cafe Details (Route Wildcard นี้จะถูกจับคู่หลังจาก Route /cafes/create)
Route::get('/cafes/{id}', [AdminCafeController::class, 'show'])->name('cafes.show');

// News Details
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');

/*
|--------------------------------------------------------------------------
| User Authentication (สำหรับ User ทั่วไป)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [UserAuthController::class, 'register'])->name('register.post');
});

/*
|--------------------------------------------------------------------------
| Admin Authentication (สำหรับ Admin)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:admin')->group(function () {
    Route::get('/login-admin', [AdminAuthController::class, 'showLogin'])->name('login.admin');
    Route::post('/login-admin', [AdminAuthController::class, 'login'])->name('login.admin.post');
    Route::get('/register-admin', [AdminAuthController::class, 'showRegister'])->name('register.admin');
    Route::post('/register-admin', [AdminAuthController::class, 'register'])->name('register.admin.post');
});

/*
|--------------------------------------------------------------------------
| Admin Protected Routes (สำหรับ Admin ที่ Login แล้ว)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->group(function () {
    Route::get('/home-admin', [AdminAuthController::class, 'home'])->name('home.admin');
    Route::post('/logout-admin', [AdminAuthController::class, 'logout'])->name('logout.admin');

    // Admin Cafe Management
    Route::resource('admin/cafe', AdminCafeController::class)->names('admin.cafe');
    Route::put('admin/cafe/{cafe}/status', [AdminCafeController::class, 'updateStatus'])->name('admin.cafe.update_status');
    Route::post('admin/cafe/check-coordinates', [AdminCafeController::class, 'checkCoordinates'])->name('admin.cafe.check_coordinates');

    // Admin News Management
    Route::prefix('admin/news')->name('admin.news.')->group(function () {
        Route::get('/', [AddnewsAdminController::class, 'addNews'])->name('add');
        Route::post('/store', [AddnewsAdminController::class, 'storeNews'])->name('store');
        Route::get('/edit/{id}', [AddnewsAdminController::class, 'editNews'])->name('edit');
        Route::put('/update/{id}', [AddnewsAdminController::class, 'updateNews'])->name('update');
        Route::delete('/delete/{id}', [AddnewsAdminController::class, 'deleteNews'])->name('delete');
        Route::patch('/toggle/{id}', [AddnewsAdminController::class, 'toggleVisibility'])->name('toggle');
        Route::post('/delete-image/{id}', [AddnewsAdminController::class, 'deleteImage'])->name('deleteImage');
    });

    // Admin Profile Management
    Route::get('/edit-profile', [AdminAuthController::class, 'editProfile'])->name('admin.edit.profile');
    Route::post('/edit-profile', [AdminAuthController::class, 'updateProfile'])->name('admin.update.profile');
});



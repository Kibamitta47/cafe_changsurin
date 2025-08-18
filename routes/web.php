<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\AdminCafeController;
use App\Http\Controllers\UserCafeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AddnewsAdminController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Auth\LineLoginController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\LineBotController;
use App\Http\Controllers\UserReviewController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [AdminCafeController::class, 'welcome'])->name('welcome');
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/add-line', [PageController::class, 'showLinePage'])->name('line.add');
Route::get('/advertising-packages', [PageController::class, 'showAdvertisingPackages'])->name('advertising.packages');
// Route สำหรับแสดงหน้าข้อมูลการแจ้งปัญหา
Route::get('/report-a-problem', [PageController::class, 'showProblemInfoPage'])->name('problem.info');
// Route สำหรับแสดงหน้าเกี่ยวกับเรา
Route::get('/about-us', [PageController::class, 'showAboutPage'])->name('about.us');

/*
|--------------------------------------------------------------------------
| User Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register');
    Route::get('/auth/line/redirect', [LineLoginController::class, 'redirectToLine'])->name('line.login');
    Route::get('/auth/line/callback', [LineLoginController::class, 'handleLineCallback']);
});

/*
|--------------------------------------------------------------------------
| User Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->name('user.')->group(function () {
    // Dashboard & Profile
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', fn() => view('user.profile'))->name('profile.show');
    Route::put('/profile', [UserAuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

    // ✅ สร้างคาเฟ่ (ต้องล็อกอินก่อน)
    Route::get('/cafes/create', [UserCafeController::class, 'create'])->name('cafes.create');

    // User's Cafe Management
    Route::prefix('cafes')->name('cafes.')->group(function() {
        Route::post('/', [UserCafeController::class, 'store'])->name('store');
        Route::post('/{cafe}/toggle-like', [UserCafeController::class, 'toggleLike'])->name('toggleLike');
        Route::get('/{cafe}/edit', [UserCafeController::class, 'edit'])->name('edit');
        Route::put('/{cafe}', [UserCafeController::class, 'update'])->name('update');
        Route::delete('/{cafe}', [UserCafeController::class, 'destroy'])->name('destroy');
    });

    // Listing Pages for User
    Route::get('/my-cafes', [UserCafeController::class, 'myCafes'])->name('cafes.my');
    Route::get('/my-liked-cafes', [UserCafeController::class, 'myLikedCafes'])->name('cafes.myLiked');
    
    // User's Review Management
    Route::prefix('reviews')->name('reviews.')->group(function() {
        Route::get('/create/{cafe_id}', [ReviewController::class, 'create'])->name('create');
        Route::post('/', [ReviewController::class, 'store'])->name('store');
        Route::get('/{review}/edit', [UserReviewController::class, 'edit'])->name('edit');
        Route::put('/{review}', [UserReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [UserReviewController::class, 'destroy'])->name('destroy');
    });

    // Listing Page for User's Reviews
    Route::get('/my-reviews', [UserReviewController::class, 'index'])->name('reviews.my');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest:admin')->group(function () {
    Route::get('/login-admin', [AdminAuthController::class, 'showLogin'])->name('login.admin');
    Route::post('/login-admin', [AdminAuthController::class, 'login'])->name('login.admin.post');
    Route::get('/register-admin', [AdminAuthController::class, 'showRegister'])->name('register.admin');
    Route::post('/register-admin', [AdminAuthController::class, 'register'])->name('register.admin.post');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/home-admin', [AdminAuthController::class, 'home'])->name('home.admin');
    Route::post('/logout-admin', [AdminAuthController::class, 'logout'])->name('logout.admin');
    Route::resource('admin/cafe', AdminCafeController::class)->names('admin.cafe');
    Route::put('admin/cafe/{cafe}/status', [AdminCafeController::class, 'updateStatus'])->name('admin.cafe.update_status');
    Route::post('admin/cafe/check-coordinates', [AdminCafeController::class, 'checkCoordinates'])->name('admin.cafe.check_coordinates');

    Route::prefix('admin/news')->name('admin.news.')->group(function () {
        Route::get('/', [AddnewsAdminController::class, 'addNews'])->name('add');
        Route::post('/store', [AddnewsAdminController::class, 'storeNews'])->name('store');
        Route::get('/edit/{id}', [AddnewsAdminController::class, 'editNews'])->name('edit');
        Route::put('/update/{id}', [AddnewsAdminController::class, 'updateNews'])->name('update');
        Route::delete('/delete/{id}', [AddnewsAdminController::class, 'deleteNews'])->name('delete');
        Route::patch('/toggle/{id}', [AddnewsAdminController::class, 'toggleVisibility'])->name('toggle');
        Route::post('/delete-image/{id}', [AddnewsAdminController::class, 'deleteImage'])->name('deleteImage');
    });

    Route::get('/edit-profile', [AdminAuthController::class, 'editProfile'])->name('admin.edit.profile');
    Route::post('/edit-profile', [AdminAuthController::class, 'updateProfile'])->name('admin.update.profile');
});

/*
|--------------------------------------------------------------------------
| Route ด้านล่างสุดที่ต้องมา "หลังสุด" เพื่อไม่แย่งกับ /cafes/create
|--------------------------------------------------------------------------
*/
Route::get('/cafes/{id}', [AdminCafeController::class, 'show'])->name('cafes.show');

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
|
| These routes are accessible to everyone, including guests.
|
*/
Route::get('/', [AdminCafeController::class, 'welcome'])->name('welcome');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/add-line', [PageController::class, 'showLinePage'])->name('line.add');
Route::get('/advertising-packages', [PageController::class, 'showAdvertisingPackages'])->name('advertising.packages');
Route::get('/report-a-problem', [PageController::class, 'showProblemInfoPage'])->name('problem.info');
Route::get('/about-us', [PageController::class, 'showAboutPage'])->name('about.us');
Route::get('/top-10-cafes', [PageController::class, 'showTop10Page'])->name('cafes.top10');
Route::get('/newly-cafes', [PageController::class, 'showNewlyCafesPage'])->name('cafes.newly');
Route::get('/FAQ-cafes', [PageController::class, 'showFAQPage'])->name('cafes.faq');



/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Routes for user login, registration, and socialite logins.
|
*/
Route::middleware('guest')->group(function () {
    // User Auth
    Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [UserAuthController::class, 'register'])->name('register.post');

    // Line Auth
    Route::get('/auth/line/redirect', [LineLoginController::class, 'redirectToLine'])->name('line.login');
    Route::get('/auth/line/callback', [LineLoginController::class, 'handleLineCallback']);
    
    // Admin Auth
    Route::get('/login-admin', [AdminAuthController::class, 'showLogin'])->name('login.admin');
    Route::post('/login-admin', [AdminAuthController::class, 'login'])->name('login.admin.post');
    Route::get('/register-admin', [AdminAuthController::class, 'showRegister'])->name('register.admin');
    Route::post('/register-admin', [AdminAuthController::class, 'register'])->name('register.admin.post');
});


/*
|--------------------------------------------------------------------------
| User Protected Routes
|--------------------------------------------------------------------------
|
| These routes require a user to be logged in.
|
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/profile', fn() => view('user.profile'))->name('user.profile.show');
    Route::put('/profile', [UserAuthController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('user.logout');

    // My Cafes (Owned by User)
    Route::get('/my-cafes', [UserCafeController::class, 'myCafes'])->name('user.cafes.my');
    Route::get('/cafes/create', [UserCafeController::class, 'create'])->name('user.cafes.create');
    Route::post('/cafes', [UserCafeController::class, 'store'])->name('user.cafes.store');
    Route::get('/cafes/{cafe}/edit', [UserCafeController::class, 'edit'])->name('user.cafes.edit');
    Route::put('/cafes/{cafe}', [UserCafeController::class, 'update'])->name('user.cafes.update');
    Route::delete('/cafes/{cafe}', [UserCafeController::class, 'destroy'])->name('user.cafes.destroy');

    // Liked Cafes
    Route::get('/my-liked-cafes', [UserCafeController::class, 'myLikedCafes'])->name('user.cafes.myLiked');
    
    // ✅✅✅ THIS IS THE CORRECT LIKE/UNLIKE ROUTE ✅✅✅
    Route::post('/cafes/{cafe:cafe_id}/toggle-like', [UserCafeController::class, 'toggleLike'])->name('cafes.toggle-like');

    // My Reviews
    Route::get('/my-reviews', [UserReviewController::class, 'index'])->name('user.reviews.my');
    Route::get('/reviews/create/{cafe_id}', [ReviewController::class, 'create'])->name('user.reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('user.reviews.store');
    Route::get('/reviews/{review}/edit', [UserReviewController::class, 'edit'])->name('user.reviews.edit');
    Route::put('/reviews/{review}', [UserReviewController::class, 'update'])->name('user.reviews.update');
    Route::delete('/reviews/{review}', [UserReviewController::class, 'destroy'])->name('user.reviews.destroy');
});


/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
|
| These routes require an admin to be logged in.
|
*/
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/home', [AdminAuthController::class, 'home'])->name('home');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::resource('cafe', AdminCafeController::class);
    Route::put('cafe/{cafe}/status', [AdminCafeController::class, 'updateStatus'])->name('cafe.update_status');
    Route::post('cafe/check-coordinates', [AdminCafeController::class, 'checkCoordinates'])->name('cafe.check_coordinates');

    Route::get('/recommend-cafes', [AdminCafeController::class, 'recommend'])->name('recommend');
    
    // ✅✅✅ แก้ไขตรงนี้: เปลี่ยนเมธอดจาก POST เป็น PUT และเปลี่ยนชื่อ route ให้ตรงกับที่ฟอร์มเรียกใช้ ✅✅✅
    Route::get('/profile/edit', [AdminAuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [AdminAuthController::class, 'updateProfile'])->name('update.profile'); // เปลี่ยนชื่อจาก profile.update เป็น update.profile

    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [AddnewsAdminController::class, 'addNews'])->name('add');
        Route::post('/store', [AddnewsAdminController::class, 'storeNews'])->name('store');
        Route::get('/edit/{news}', [AddnewsAdminController::class, 'editNews'])->name('edit');
        Route::put('/update/{news}', [AddnewsAdminController::class, 'updateNews'])->name('update');
        Route::delete('/delete/{news}', [AddnewsAdminController::class, 'deleteNews'])->name('delete');
        Route::patch('/toggle/{news}', [AddnewsAdminController::class, 'toggleVisibility'])->name('toggle');
        Route::post('/delete-image/{news}', [AddnewsAdminController::class, 'deleteImage'])->name('deleteImage');
    });
});


/*
|--------------------------------------------------------------------------
| Public Cafe Detail Route (Should be last)
|--------------------------------------------------------------------------
|
| This route is for viewing a single cafe's details.
|
*/
Route::get('/cafes/{cafe}', [AdminCafeController::class, 'show'])->name('cafes.show');
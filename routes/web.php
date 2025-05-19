<?php

use App\Http\Controllers\AuthController;

Route::middleware('guest:admin')->group(function () {
    Route::get('/login-admin', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login-admin', [AuthController::class, 'login']);
    Route::get('/register-admin', [AuthController::class, 'showRegister']);
    Route::post('/register-admin', [AuthController::class, 'register']);
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/home-admin', [AuthController::class, 'home'])->name('home');
    Route::post('/logout-admin', [AuthController::class, 'logout'])->name('logout');
    Route::get('/increase-admin', [AuthController::class, 'showIncreaseForm']);
    Route::get('/addnews-admin', [AuthController::class, 'addNews']);
    Route::post('/submit-promotion', [AuthController::class, 'storeNews']);
    Route::get('/edit-profile', [AuthController::class, 'editProfile'])->name('edit.profile');
    Route::post('/update-profile', [AuthController::class, 'updateProfile'])->name('update.profile');
    Route::get('/news/{id}/edit', [AuthController::class, 'editNews'])->name('news.edit');
    Route::delete('/news/{id}', [AuthController::class, 'deleteNews'])->name('news.delete');
    Route::put('/news/{id}', [AuthController::class, 'updateNews'])->name('news.update');
    
    // route สำหรับหน้า review
    Route::get('/review', function () {
        return view('review');
    });
});

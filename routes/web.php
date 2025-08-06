<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\User\DesignController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\RiwayatPesananController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PromoController as AdminPromoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// all
Route::get('/', [UserController::class, 'index'])->name('home');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
Route::get('/create', function () { return view('create'); })->name('create');


// login & register
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'registerPost']);
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/notifications',[NotificationController::class, 'fetch'])->name('notifications.fetch');


//  user
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/ukuran', function () { return view('user.ukuran'); })->name('ukuran');
    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    Route::get('/checkout', [OrderController::class, 'showCheckout'])->name('checkout.show');
    Route::post('/checkout', [OrderController::class, 'storeOrder'])->name('checkout.store');
    Route::post('/promo', [OrderController::class, 'applyPromo'])->name('promo.apply');
    Route::get('/remove-promo', [OrderController::class, 'removePromo'])->name('promo.remove');
    Route::get('/receipt/{order}', [OrderController::class, 'showReceipt'])->name('receipt');
    Route::post('/order/{order}/upload-proof', [OrderController::class, 'uploadProof'])->name('order.upload.proof');
    Route::get('/history', [RiwayatPesananController::class, 'index'])->name('history');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notification.markAsRead');

    Route::patch('/designs/{design}', [DesignController::class, 'update'])->name('designs.update');
    Route::delete('/designs/{design}', [DesignController::class, 'destroy'])->name('designs.destroy');
    Route::post('/designs', [DesignController::class, 'store'])->name('designs.store');
    Route::post('/designs/{design}/comments', [DesignController::class, 'addComment'])->name('designs.comments.add');
    Route::post('/designs/{design}/like', [DesignController::class, 'toggleLike'])->name('designs.like');
});


// admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminUserController::class, 'index'])->name('home');
    
    Route::get('/profile', function () {
        return view('admin.profile', ['user' => Auth::user()]);
    })->name('profile');

    Route::post('/users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');
    Route::post('/promo', [PromoController::class, 'store'])->name('promo.store');
    Route::get('/users/{userId}/orders', [AdminOrderController::class, 'getUserOrders'])->name('admin.users.orders');
    
    Route::resource('users', AdminUserController::class);
    Route::resource('orders', AdminOrderController::class);
    Route::resource('promos', AdminPromoController::class);
    Route::resource('notifications', NotificationController::class);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/report/promo-details/{code}', [ReportController::class, 'getPromoDetails'])->name('admin.reports.promoDetails');
    Route::get('/report/sales-details/{year}/{month}', [ReportController::class, 'getMonthlyDetails'])->name('admin.report.salesDetails');
    Route::get('/report/user-details/{year}/{month}', [ReportController::class, 'getUserDetails'])->name('admin.reports.userDetails');
    Route::get('/report/customer-details/{userId}', [ReportController::class, 'getCustomerDetails'])->name('admin.reports.customerDetails');

    Route::get('/notifications/update', [NotificationController::class, 'update'])->name('notifications.update');
    Route::get('/notifications/destroy', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
});


// email verification
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('home');
    }
    if ($request->user()->markEmailAsVerified()) {
        event(new \Illuminate\Auth\Events\Verified($request->user()));
    }
    return redirect()->route('home')->with('verified', true);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CourierController as AdminCourierController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeliveryController as AdminDeliveryController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Courier\DeliveryController as CourierDeliveryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Katalog publik (toko)
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Webhook Midtrans (tanpa auth, dikecualikan dari CSRF - lihat bootstrap/app.php)
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');

// Chatbot customer service (publik, dibatasi 30 pesan/menit per pengunjung)
Route::post('/chatbot', [ChatbotController::class, 'ask'])->middleware('throttle:30,1')->name('chatbot.ask');

// Halaman kurir tanpa login: akses lewat link rahasia yang dibuat admin (dikecualikan dari CSRF - lihat bootstrap/app.php)
Route::prefix('kurir/{delivery:token}')->name('courier.')->middleware('throttle:60,1')->group(function () {
    Route::get('/', [CourierDeliveryController::class, 'show'])->name('show');
    Route::post('/mulai', [CourierDeliveryController::class, 'start'])->name('start');
    Route::post('/lokasi', [CourierDeliveryController::class, 'location'])->name('location');
    Route::post('/selesai', [CourierDeliveryController::class, 'complete'])->name('complete');
});

// Dashboard: user biasa lihat ringkasan pesanan sendiri
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Keranjang & checkout
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/keranjang/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{itemId}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/bayar', [PaymentController::class, 'pay'])->name('payment.pay');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/pesanan/{order}/tracking', [OrderController::class, 'tracking'])->name('orders.tracking');
});

// Area admin: dashboard terpisah dari user biasa
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class)->except('show');
    Route::get('categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::delete('categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    // Kurir & pengiriman
    Route::resource('kurir', AdminCourierController::class)->parameters(['kurir' => 'courier'])->except(['show', 'create'])->names('couriers');
    Route::get('pengiriman', [AdminDeliveryController::class, 'index'])->name('deliveries.index');
    Route::post('orders/{order}/pengiriman', [AdminDeliveryController::class, 'store'])->name('deliveries.store');
    Route::patch('pengiriman/{delivery}', [AdminDeliveryController::class, 'update'])->name('deliveries.update');
    Route::delete('pengiriman/{delivery}', [AdminDeliveryController::class, 'destroy'])->name('deliveries.destroy');
    Route::get('pengiriman/{delivery}/tracking', [AdminDeliveryController::class, 'tracking'])->name('deliveries.tracking');

    // Chatbot customer service
    Route::delete('chatbot/belum-terjawab', [AdminFaqController::class, 'dismiss'])->name('faqs.dismiss');
    Route::resource('chatbot', AdminFaqController::class)->parameters(['chatbot' => 'faq'])->except('show')->names('faqs');

    Route::get('laporan', [AdminReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';

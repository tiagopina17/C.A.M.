<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreManagementController;
use App\Http\Controllers\StoreRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explorar', [ExploreController::class, 'index'])->name('explore');
Route::get('/mapa', [MapController::class, 'index'])->name('map');

Route::middleware('auth')->group(function () {
    Route::get('/lojas/registar', [StoreRegistrationController::class, 'create'])->name('stores.create');
    Route::post('/lojas/registar', [StoreRegistrationController::class, 'store'])->name('stores.store');
    Route::get('/lojas/juntar', [StoreManagementController::class, 'showJoinForm'])->name('stores.join');
    Route::post('/lojas/juntar', [StoreManagementController::class, 'join'])->name('stores.join.submit');

    Route::get('/gestao/lojas', [StoreManagementController::class, 'index'])->name('management.stores.index');
    Route::get('/gestao/lojas/{store}', [StoreManagementController::class, 'edit'])->name('management.stores.edit');
    Route::put('/gestao/lojas/{store}', [StoreManagementController::class, 'update'])->name('management.stores.update');
    Route::delete('/gestao/lojas/{store}/colaboradores/{user}', [StoreManagementController::class, 'removeEmployee'])->name('management.stores.remove_employee');
    Route::post('/gestao/lojas/{store}/convites', [StoreManagementController::class, 'generateInviteCode'])->name('management.stores.generate_code');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

Route::get('/lojas/{store}', [StoreController::class, 'show'])->name('stores.show');
Route::get('/api/stores/search', [HomeController::class, 'search'])->name('stores.search');

Route::get('/ajuda', [InfoController::class, 'help'])->name('help');
Route::get('/contacto', [InfoController::class, 'contact'])->name('contact');
Route::post('/contacto', [InfoController::class, 'submitContact'])->name('contact.submit');

require __DIR__.'/auth.php';

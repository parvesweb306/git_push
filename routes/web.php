<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get(
    '/family/{code}',
    [DeviceController::class, 'familyInstall']
)->name('family.install');


Route::get('/privacy', function () {
    return view('privacy');
});

Route::middleware([
    'auth',
    'verified'
])->group(function () {



    Route::get(
        '/dashboard',
        [DeviceController::class, 'index']
    )->name('dashboard');

    Route::get(
        '/dashboard/status',
        [DeviceController::class, 'dashboardStatus']
    )->name('dashboard.status');

    Route::get(
    '/telegram/connect',
    [\App\Http\Controllers\TelegramController::class, 'connect']
)->name('telegram.connect');

    Route::post(
        '/devices/create-pairing',
        [DeviceController::class, 'createPairing']
    )->name('devices.create-pairing');

    Route::post(
        '/devices/{device}/lock',
        [DeviceController::class, 'lock']
    )->name('devices.lock');

    Route::post(
        '/devices/{device}/unlock',
        [DeviceController::class, 'unlock']
    )->name('devices.unlock');

    Route::post(
        '/devices/{device}/set-unlock-pin',
        [DeviceController::class, 'setUnlockPin']
    )->name('devices.set-unlock-pin');

    Route::post(
        '/devices/{device}/generate-unlock-code',
        [DeviceController::class, 'generateUnlockCode']
    )->name('devices.generate-unlock-code');

    Route::delete(
        '/devices/{device}',
        [DeviceController::class, 'destroy']
    )->name('devices.destroy');
});

/*
|--------------------------------------------------------------------------
| Main Admin — Manage Sub-Users
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'admin',
])->prefix('admin')->name('admin.')->group(function () {

    Route::get(
        '/users',
        [UserController::class, 'index']
    )->name('users.index');

    Route::post(
        '/users',
        [UserController::class, 'store']
    )->name('users.store');

    Route::delete(
        '/users/{user}',
        [UserController::class, 'destroy']
    )->name('users.destroy');
});

/*
|--------------------------------------------------------------------------
| Pairing / Download Link
|--------------------------------------------------------------------------
*/

Route::get(
    '/dl/{code}',
    [DeviceController::class, 'downloadPage']
)->name('devices.download-page');

Route::get(
    '/dl/{code}/apk',
    [DeviceController::class, 'downloadApk']
)->name('devices.download-apk');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});

require __DIR__ . '/auth.php';
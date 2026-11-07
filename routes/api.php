<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;


/*
|--------------------------------------------------------------------------
| Standard Laravel User API
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->get(
        '/user',
        function (Request $request) {
            return $request->user();
        }
    );




Route::post(
    '/devices/enroll-family',
    [DeviceController::class, 'enrollFamily']
)->middleware('throttle:10,1');
/*
|--------------------------------------------------------------------------
| Child Device Pairing
|--------------------------------------------------------------------------
*/

Route::post(
    '/devices/pair',
    [
        DeviceController::class,
        'pair',
    ]
)->middleware(
    'throttle:10,1'
);

Route::post(
    '/devices/activate-after-setup',
    [
        DeviceController::class,
        'activateAfterSetup',
    ]
)->middleware(
    'throttle:10,1'
);


/*
|--------------------------------------------------------------------------
| Child Device Status
|--------------------------------------------------------------------------
*/

Route::get(
    '/devices/status/{token}',
    [
        DeviceController::class,
        'status',
    ]
)->middleware(
    'throttle:120,1'
);


Route::post(
    '/telegram/webhook/{secret}',
    [\App\Http\Controllers\TelegramController::class, 'webhook']
)->middleware('throttle:60,1');

/*
|--------------------------------------------------------------------------
| Unlock With 6 Digit Code
|--------------------------------------------------------------------------
*/

Route::post(
    '/devices/unlock-with-code',
    [
        DeviceController::class,
        'unlockWithCode',
    ]
)->middleware(
    'throttle:10,1'
);
<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Services\TelegramService;
class DeviceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */


public function index(Request $request)
{
    $user = $request->user();

    if (empty($user->family_invite_code)) {
        do {
            $code = strtoupper(Str::random(32));
        } while (
            User::where('family_invite_code', $code)->exists()
        );

        $user->forceFill([
            'family_invite_code' => $code,
        ])->save();
    }

    $devices = Device::where('user_id', $user->id)
        ->latest()
        ->get();

    $familyLink = route(
        'family.install',
        $user->family_invite_code
    );

    return view('dashboard', compact('devices', 'familyLink'));
}


    /*
    |--------------------------------------------------------------------------
    | Create New Device
    |--------------------------------------------------------------------------
    */

    public function createPairing(Request $request)
    {
        $request->validate([
            'device_name' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        do {
            $pairingCode = strtoupper(
                Str::random(6)
            );
        } while (
            Device::where(
                'pairing_code',
                $pairingCode
            )->exists()
        );

        $device = Device::create([
            'user_id' => $request->user()->id,

            'device_name' =>
                $request->device_name,

            'pairing_code' =>
                $pairingCode,

            'device_token' =>
                null,

            'is_locked' =>
                false,

            'unlock_code_hash' =>
                null,

            'unlock_code_expires_at' =>
                null,

            'last_seen' =>
                null,
        ]);

        return back()->with(
            'device_created',
            $device->id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Lock Device
    |--------------------------------------------------------------------------
    */

    public function lock(
        Request $request,
        Device $device
    ) {
        $this->authorizeDevice(
            $request,
            $device
        );

        if (
            empty($device->device_token)
        ) {
            return back()->with(
                'error',
                'এই ডিভাইস এখনো APK থেকে paired হয়নি।'
            );
        }

        $device->update([
            'is_locked' => true,

            /*
             * পুরনো unlock code invalid
             * করে দেওয়া হচ্ছে।
             */
            'unlock_code_hash' => null,

            'unlock_code_expires_at' => null,
        ]);

        return back()->with(
            'success',
            $device->device_name .
            ' এখন LOCK করা হয়েছে।'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Unlock Device Directly From Dashboard
    |--------------------------------------------------------------------------
    */

    public function unlock(
        Request $request,
        Device $device
    ) {
        $this->authorizeDevice(
            $request,
            $device
        );

        $device->update([
            'is_locked' => false,

            'unlock_code_hash' => null,

            'unlock_code_expires_at' => null,
        ]);

        return back()->with(
            'success',
            $device->device_name .
            ' UNLOCK করা হয়েছে।'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Generate 6 Digit Unlock Code
    |--------------------------------------------------------------------------
    */

    public function generateUnlockCode(
        Request $request,
        Device $device
    ) {
        $this->authorizeDevice(
            $request,
            $device
        );

        if (
            empty($device->device_token)
        ) {
            return back()->with(
                'error',
                'ডিভাইস এখনো paired হয়নি।'
            );
        }

        if (!$device->is_locked) {
            return back()->with(
                'error',
                'ডিভাইস বর্তমানে locked নয়।'
            );
        }

        /*
         * Secure random 6 digit code.
         */
        $code = (string) random_int(
            100000,
            999999
        );

        $device->update([
            'unlock_code_hash' =>
                Hash::make($code),

            /*
             * Code 30 minutes valid.
             */
            'unlock_code_expires_at' =>
                now()->addMinutes(30),
        ]);

        return back()->with(
            'unlock_code',
            $code
        );
    }


    /*
    |--------------------------------------------------------------------------
    | API - Pair Device
    |--------------------------------------------------------------------------
    */

 
public function pair(Request $request)
{
    $validated = $request->validate([
        'pairing_code' => [
            'required',
            'string',
            'size:6',
            'regex:/^[A-Za-z0-9]{6}$/',
        ],
    ]);

    $code = strtoupper(trim($validated['pairing_code']));

    $pairedDevice = DB::transaction(function () use ($code) {
        $device = Device::where('pairing_code', $code)
            ->lockForUpdate()
            ->first();

        if (!$device || !empty($device->device_token)) {
            return null;
        }

        $token = Str::random(80);

        $device->update([
            'device_token' => $token,
            'last_seen' => now(),
        ]);

        return [
            'device_token' => $token,
            'device_id' => $device->id,
            'device_name' => $device->device_name,
            'is_locked' => (bool) $device->is_locked,
        ];
    });

    if ($pairedDevice === null) {
        return response()->json([
            'success' => false,
            'error' => 'Invalid, expired, or already-used pairing code.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Device paired successfully.',
        'device_token' => $pairedDevice['device_token'],
        'device_id' => $pairedDevice['device_id'],
        'device_name' => $pairedDevice['device_name'],
        'is_locked' => $pairedDevice['is_locked'],
    ]);
}


    /*
    |--------------------------------------------------------------------------
    | API - Activate Paired Device After Setup
    |--------------------------------------------------------------------------
    */

    public function activateAfterSetup(
        Request $request
    ) {
        $request->validate([
            'device_token' => [
                'required',
                'string',
            ],
        ]);

        $device = Device::where(
            'device_token',
            $request->device_token
        )->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'error' => 'Device not found.',
            ], 404);
        }

        $device->update([
            'is_locked' => true,
            'unlock_code_hash' => null,
            'unlock_code_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'is_locked' => true,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | API - Device Status
    |--------------------------------------------------------------------------
    */

    public function status(
        string $token
    ) {
        $device = Device::where(
            'device_token',
            $token
        )->first();

        if (!$device) {
            return response()->json([
                'success' => false,

                'error' =>
                    'Device not found.',
            ], 404);
        }

        /*
         * Update heartbeat.
         */
        $device->update([
            'last_seen' => now(),
        ]);

        return response()->json([
            'success' => true,

            'device_id' =>
                $device->id,

            'device_name' =>
                $device->device_name,

            'is_locked' =>
                (bool) $device->is_locked,

            'server_time' =>
                now()->toIso8601String(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | API - Unlock With Code
    |--------------------------------------------------------------------------
    */

    public function unlockWithCode(
        Request $request
    ) {
        $request->validate([
            'device_token' => [
                'required',
                'string',
            ],

            'code' => [
                'required',
                'digits:6',
            ],
        ]);

        $device = Device::where(
            'device_token',
            $request->device_token
        )->first();

        if (!$device) {
            return response()->json([
                'success' => false,

                'error' =>
                    'Device not found.',
            ], 404);
        }

        /*
         * Check code exists.
         */
        if (
            empty(
                $device->unlock_code_hash
            )
        ) {
            return response()->json([
                'success' => false,

                'error' =>
                    'No unlock code is active.',
            ], 403);
        }

        /*
         * Check expiry.
         */
        if (
            empty(
                $device->unlock_code_expires_at
            ) ||
            now()->greaterThan(
                $device->unlock_code_expires_at
            )
        ) {
            return response()->json([
                'success' => false,

                'error' =>
                    'Unlock code expired.',
            ], 403);
        }

        /*
         * Verify hash.
         */
        if (
            !Hash::check(
                $request->code,
                $device->unlock_code_hash
            )
        ) {
            return response()->json([
                'success' => false,

                'error' =>
                    'Invalid unlock code.',
            ], 403);
        }

        /*
         * Successful unlock.
         *
         * One-time code is destroyed.
         */
        $device->update([
            'is_locked' => false,

            'unlock_code_hash' => null,

            'unlock_code_expires_at' => null,

            'last_seen' => now(),
        ]);

        return response()->json([
            'success' => true,

            'message' =>
                'Device unlocked successfully.',

            'is_locked' => false,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Download Page
    |--------------------------------------------------------------------------
    */

 
public function downloadPage(string $code)
{
    $code = strtoupper(trim($code));

    $device = Device::where('pairing_code', $code)->first();

    if (!$device) {
        abort(404, 'Invalid download link.');
    }

    $packageId = (string) config('services.play_store.package_id');

    if ($packageId === '') {
        abort(500, 'Play Store package ID is not configured.');
    }

    $referrer = rawurlencode('pairing_code=' . $device->pairing_code);

    $playStoreUrl = 'https://play.google.com/store/apps/details'
        . '?id=' . rawurlencode($packageId)
        . '&referrer=' . $referrer;

    return redirect()->away($playStoreUrl);
}


    /*
    |--------------------------------------------------------------------------
    | Download APK
    |--------------------------------------------------------------------------
    */

    public function downloadApk(
        string $code
    ) {
        $code = strtoupper(
            trim($code)
        );

        $device = Device::where(
            'pairing_code',
            $code
        )->first();

        if (!$device) {
            abort(
                404,
                'Invalid download link.'
            );
        }

        $apkPath =
            'parental-app.apk';

        if (
            !Storage::disk('public')
                ->exists($apkPath)
        ) {
            abort(
                404,
                'APK file is not available.'
            );
        }

        return Storage::disk('public')
            ->download(
                $apkPath,
                'Child-Guard.apk',
                [
                    'Content-Type' =>
                        'application/vnd.android.package-archive',
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Dashboard Status API
    |--------------------------------------------------------------------------
    */

    public function dashboardStatus(
        Request $request
    ) {
        $devices = Device::where(
            'user_id',
            $request->user()->id
        )
        ->latest()
        ->get();

        return response()->json([
            'success' => true,

            'devices' =>
                $devices->map(
                    function ($device) {

                        return [
                            'id' =>
                                $device->id,

                            'device_name' =>
                                $device->device_name,

                            'is_locked' =>
                                (bool) $device->is_locked,

                            'paired' =>
                                !empty(
                                    $device->device_token
                                ),

                            'last_seen' =>
                                $device->last_seen
                                    ? $device->last_seen
                                        ->toIso8601String()
                                    : null,
                        ];
                    }
                ),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Security Helper
    |--------------------------------------------------------------------------
    */

    private function authorizeDevice(
        Request $request,
        Device $device
    ): void {
        abort_unless(
            $device->user_id ===
                $request->user()->id,
            403
        );
    }




public function familyInstall(string $code)
{
    $code = strtoupper(trim($code));

    $user = User::where('family_invite_code', $code)->first();

    if (!$user) {
        abort(404, 'Invalid family install link.');
    }

    $packageId = (string) config('services.play_store.package_id');

    if ($packageId === '') {
        abort(500, 'Play Store package ID is not configured.');
    }

    $query = http_build_query([
        'id' => $packageId,
        'referrer' => 'family_invite=' . $user->family_invite_code,
    ]);

    return redirect()->away(
        'https://play.google.com/store/apps/details?' . $query
    );
}

public function enrollFamily(
    Request $request,
    TelegramService $telegram
) {
    $data = $request->validate([
        'family_invite' => ['required', 'string', 'size:32'],
        'installation_id' => ['required', 'uuid'],
        'device_name' => ['required', 'string', 'max:100'],

        'manufacturer' => ['nullable', 'string', 'max:100'],
        'model' => ['nullable', 'string', 'max:100'],
        'android_version' => ['nullable', 'string', 'max:50'],
        'battery_level' => ['nullable', 'integer', 'min:0', 'max:100'],
    ]);

    $inviteCode = strtoupper(trim($data['family_invite']));
    $ipAddress = $request->ip();

    // Get approximate location from public IP.
    $location = $this->getIpLocation($ipAddress);

    $device = DB::transaction(function () use (
        $inviteCode,
        $data,
        $ipAddress,
        $location
    ) {
        $parent = User::where(
            'family_invite_code',
            $inviteCode
        )
        ->lockForUpdate()
        ->first();

        if (!$parent) {
            return null;
        }

        $existingDevice = Device::where(
            'installation_id',
            $data['installation_id']
        )->first();

        $deviceData = [
            'device_name' => $data['device_name'],
            'manufacturer' => $data['manufacturer'] ?? null,
            'model' => $data['model'] ?? null,
            'android_version' => $data['android_version'] ?? null,
            'battery_level' => $data['battery_level'] ?? null,
            'ip_address' => $ipAddress,

            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'region' => $location['region'] ?? null,
            'postal_code' => $location['postal_code'] ?? null,
            'timezone' => $location['timezone'] ?? null,

            'last_seen' => now(),
        ];

        if ($existingDevice) {
            abort_unless(
                (int) $existingDevice->user_id === (int) $parent->id,
                409,
                'This installation is already linked to another family.'
            );

            // Update existing device location and information.
            $existingDevice->update($deviceData);

            return $existingDevice;
        }

        // Create a new device.
        $deviceData['user_id'] = $parent->id;
        $deviceData['pairing_code'] = null;
        $deviceData['installation_id'] = $data['installation_id'];
        $deviceData['device_token'] = Str::random(80);
        $deviceData['is_locked'] = false;

        return Device::create($deviceData);
    });

    if (!$device) {
        return response()->json([
            'success' => false,
            'error' => 'Invalid family invite link.',
        ], 404);
    }

    // Notify only when a genuinely new device is created.
    if ($device->wasRecentlyCreated) {
        $telegram->notifyNewDevice($device);
    }

    return response()->json([
        'success' => true,
        'device_id' => $device->id,
        'device_name' => $device->device_name,
        'device_token' => $device->device_token,
        'is_locked' => (bool) $device->is_locked,
    ]);
}


/**
 * Get approximate location using public IP.
 */
private function getIpLocation(string $ip): array
{
    if (!filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    )) {
        return [];
    }

    try {
        $response = Http::timeout(8)
            ->get('https://ipwho.is/' . rawurlencode($ip));

        if (
            !$response->successful() ||
            $response->json('success') !== true
        ) {
            return [];
        }

        return [
            'country' => $response->json('country'),
            'city' => $response->json('city'),
            'region' => $response->json('region'),
            'postal_code' => $response->json('postal'),
            'timezone' => $response->json('timezone.id'),
        ];

    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning(
            'IP location lookup failed.',
            ['error' => $e->getMessage()]
        );

        return [];
    }
}


}
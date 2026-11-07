<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function notifyNewDevice(Device $device): void
    {
        $device->loadMissing('user');

        $token = config('services.telegram.bot_token');

        $recipients = array_unique(array_filter([
            config('services.telegram.owner_chat_id'),
            $device->user?->telegram_chat_id,
        ]));

        if (empty($token)) {
            Log::error('Telegram: Bot token is missing.');
            return;
        }

        if (empty($recipients)) {
            Log::error('Telegram: No recipient chat ID configured.');
            return;
        }

        $ip = $device->ip_address ?: 'Unknown';
        $location = $this->getIpLocation($ip);

        $message =
            "⚡️ NEW DEVICE CONNECTED!\n\n" .
            "📱 Device: " . ($device->device_name ?: 'Unknown') . "\n" .
            "🏷️ Manufacturer: " . ($device->manufacturer ?: 'Unknown') . "\n" .
            "📲 Model: " . ($device->model ?: 'Unknown') . "\n" .
            "🤖 Android: " . ($device->android_version ?: 'Unknown') . "\n" .
            "🔋 Battery: " .
                ($device->battery_level !== null
                    ? $device->battery_level . '%'
                    : 'Unknown') . "\n" .
            "🌐 IP: " . $ip . "\n" .
            "🌍 Country: " . ($location['country'] ?? 'Unknown') . "\n" .
            "🏙️ City: " . ($location['city'] ?? 'Unknown') . "\n" .
            "🔐 Enrollment: Successful\n" .
            "🕒 Time: " . now()->toDateTimeString();

        foreach ($recipients as $chatId) {
            try {
                $response = Http::asForm()
                    ->timeout(15)
                    ->post(
                        "https://api.telegram.org/bot{$token}/sendMessage",
                        [
                            'chat_id' => $chatId,
                            'text' => $message,
                        ]
                    );

                if (!$response->successful() ||
                    $response->json('ok') !== true) {

                    Log::error('Telegram sendMessage failed.', [
                        'chat_id' => $chatId,
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);

                } else {
                    Log::info('Telegram notification sent successfully.', [
                        'chat_id' => $chatId,
                        'device_id' => $device->id,
                    ]);
                }

            } catch (\Throwable $e) {
                Log::error('Telegram notification exception.', [
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

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
                $response->successful() &&
                $response->json('success') === true
            ) {
                return [
                    'country' => $response->json('country'),
                    'city' => $response->json('city'),
                ];
            }

            Log::warning('IP location lookup returned unsuccessful response.', [
                'ip' => $ip,
                'response' => $response->body(),
            ]);

        } catch (\Throwable $e) {
            Log::warning('IP location lookup exception.', [
                'message' => $e->getMessage(),
            ]);
        }

        return [];
    }
}

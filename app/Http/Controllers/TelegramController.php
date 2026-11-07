<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    // Parent connects their Telegram account.
    public function connect(Request $request)
    {
        $botUsername = config('services.telegram.bot_username');

        if (!$botUsername) {
            return back()->with(
                'error',
                'Telegram bot username is not configured.'
            );
        }

        $plainToken = Str::random(40);

        $request->user()->forceFill([
            'telegram_link_token_hash' => hash(
                'sha256',
                $plainToken
            ),
            'telegram_link_token_expires_at' => now()->addMinutes(10),
        ])->save();

        $url = 'https://t.me/' . $botUsername .
            '?start=' . $plainToken;

        return redirect()->away($url);
    }

    // Telegram webhook receives the /start command.
    public function webhook(Request $request, string $secret)
    {
        $expected = (string) config(
            'services.telegram.webhook_secret'
        );

        abort_unless(
            $expected !== '' && hash_equals($expected, $secret),
            403
        );

        $message = $request->input('message');

        if (!is_array($message)) {
            return response()->json(['ok' => true]);
        }

        // Only private chats can be linked.
        if (($message['chat']['type'] ?? '') !== 'private') {
            return response()->json(['ok' => true]);
        }

        $text = trim($message['text'] ?? '');

        if (!preg_match(
            '/^\/start(?:@\w+)?\s+([A-Za-z0-9_-]{20,64})$/',
            $text,
            $matches
        )) {
            return response()->json(['ok' => true]);
        }

        $plainToken = $matches[1];

        $user = User::where(
            'telegram_link_token_hash',
            hash('sha256', $plainToken)
        )
        ->where(
            'telegram_link_token_expires_at',
            '>',
            now()
        )
        ->first();

        if (!$user) {
            return response()->json(['ok' => true]);
        }

        $user->forceFill([
            'telegram_chat_id' => (string) $message['chat']['id'],
            'telegram_link_token_hash' => null,
            'telegram_link_token_expires_at' => null,
        ])->save();

        return response()->json(['ok' => true]);
    }
}
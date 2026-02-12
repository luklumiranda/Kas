<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    private $botToken;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
    }

    /**
     * Webhook untuk menerima pesan dari Telegram
     */
    public function webhook(Request $request)
    {
        $update = $request->all();

        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Handle pesan dari user
     */
    private function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $userId = $message['from']['id'];
        $firstName = $message['from']['first_name'] ?? '';
        $lastName = $message['from']['last_name'] ?? '';
        $username = $message['from']['username'] ?? null;

        // Simpan atau update user Telegram
        TelegramUser::updateOrCreate(
            ['telegram_id' => $userId],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
            ]
        );

        // Command /start
        if ($text === '/start') {
            $this->sendMessage($chatId, "Selamat datang! Bot ini membantu Anda melacak tagihan kas.\n\nGunakan:\n/status - Lihat status tagihan Anda\n/bantuan - Dapatkan bantuan");
        }

        // Command /status
        elseif ($text === '/status') {
            $this->showBillStatus($chatId, $userId);
        }

        // Command /bantuan
        elseif ($text === '/bantuan') {
            $this->sendMessage($chatId, "Available commands:\n/start - Mulai\n/status - Lihat tagihan Anda\n/bantuan - Bantuan");
        }
    }

    /**
     * Tampilkan status tagihan
     */
    private function showBillStatus($chatId, $telegramUserId)
    {
        $telegramUser = TelegramUser::where('telegram_id', $telegramUserId)->first();

        if (!$telegramUser || !$telegramUser->customer) {
            $this->sendMessage($chatId, "Maaf, data Anda tidak terdaftar. Silakan hubungi bendahara.");
            return;
        }

        $bills = Bill::where('customer_id', $telegramUser->customer_id)->get();

        if ($bills->isEmpty()) {
            $this->sendMessage($chatId, "Anda tidak memiliki tagihan yang tertunda. Terima kasih!");
            return;
        }

        $message = "📋 Status Tagihan Anda:\n\n";
        $totalPending = 0;

        foreach ($bills as $bill) {
            if (!$bill->isPaid()) {
                $message .= "📌 " . $bill->bill_type . ": Rp" . number_format($bill->amount, 0, ',', '.') . "\n";
                $message .= "   Jatuh Tempo: " . $bill->due_date->format('d/m/Y') . "\n";
                if ($bill->isOverdue()) {
                    $message .= "   ⚠️ OVERDUE\n";
                }
                $message .= "\n";
                $totalPending += $bill->amount;
            }
        }

        $message .= "💰 Total Tunggakan: Rp" . number_format($totalPending, 0, ',', '.');
        $this->sendMessage($chatId, $message);
    }

    /**
     * Kirim pesan ke user Telegram
     */
    public function sendMessage($chatId, $text)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        Http::post($url, [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * Broadcast pesan ke semua siswa yang memiliki tunggakan
     */
    public function broadcastDebtReminder(Request $request)
    {
        $this->authorize('isAdmin'); // Hanya admin/bendahara

        $overdueBills = Bill::overdue()->with('customer.telegramUser')->get();

        $successCount = 0;
        foreach ($overdueBills as $bill) {
            if ($bill->customer->telegramUser) {
                $message = "Halo {$bill->customer->name},\n\n";
                $message .= "Hanya mengingatkan bahwa Anda memiliki tunggakan kas sebesar:\n";
                $message .= "💰 Rp" . number_format($bill->amount, 0, ',', '.') . "\n\n";
                $message .= "Tipe: {$bill->bill_type}\n";
                $message .= "Jatuh Tempo: {$bill->due_date->format('d/m/Y')}\n\n";
                $message .= "Yuk, segera lunasi ke bendahara. Terima kasih! 🙏";

                $this->sendMessage($bill->customer->telegramUser->telegram_id, $message);
                $successCount++;
            }
        }

        return redirect()->back()->with('success', "Reminder telah dikirim ke {$successCount} siswa");
    }

    /**
     * Setup webhook
     */
    public function setupWebhook()
    {
        $url = url('/telegram/webhook');
        $apiUrl = "https://api.telegram.org/bot{$this->botToken}/setWebhook";

        $response = Http::post($apiUrl, [
            'url' => $url,
        ]);

        return response()->json($response->json());
    }

    /**
     * Get webhook status
     */
    public function getWebhookStatus()
    {
        $apiUrl = "https://api.telegram.org/bot{$this->botToken}/getWebhookInfo";
        $response = Http::get($apiUrl);

        return response()->json($response->json());
    }
}

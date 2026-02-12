<?php

namespace App\Modules\Delivery\Services;

use App\Modules\Delivery\Interfaces\NotificationInterface;
use App\Modules\Delivery\Models\OrderLog;
use Illuminate\Support\Facades\Log;

class MockNotificationService implements NotificationInterface
{
    public function send(string $recipient, string $message): bool
    {
        $startTime = microtime(true);

        // Log to file as requested
        Log::channel('daily')->info("SMS to {$recipient}: {$message}");

        // Also specific file if needed, but daily log is fine for "mock - logging to file" 
        // unless specific sms.log is strictly required. 
        // Prompt says: "Mock-SMS: logging in storage/logs/sms.log"
        // I'll try to append to that file specifically.

        $logLine = "[" . date('Y-m-d H:i:s') . "] To: {$recipient} | Message: {$message}" . PHP_EOL;
        file_put_contents(storage_path('logs/sms.log'), $logLine, FILE_APPEND);

        OrderLog::create([
            'service' => 'sms',
            'method' => 'send',
            'url' => 'mock://sms/send',
            'request_body' => json_encode(['recipient' => $recipient, 'message' => $message]),
            'response_body' => json_encode(['success' => true]),
            'status_code' => 200,
            'duration_ms' => (microtime(true) - $startTime) * 1000
        ]);

        return true;
    }
}

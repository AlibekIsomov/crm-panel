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

<?php
// index.php أو finnhub_webhook.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// إعدادات Telegram
$telegram_token = '8454842225:AAHRWxEtU1f9patcvyhkZMeYA5kLTh0itQs';
$chat_id = '6059267756';

// تسجيل بداية الطلب
file_put_contents('log.txt', date('Y-m-d H:i:s') . " - طلب جديد\n", FILE_APPEND);

// تحقق من السر
$secret = $_SERVER['HTTP_X_FINNHUB_SECRET'] ?? '';
$expected_secret = 'd296r4hr01qhoena9grg';

if ($secret !== $expected_secret) {
    file_put_contents('log.txt', "❌ سر غير صحيح: {$secret}\n", FILE_APPEND);
    http_response_code(403);
    exit('Invalid secret');
}

// استقبال البيانات
$data = json_decode(file_get_contents('php://input'), true);

// استخراج البيانات
$title = $data['headline'] ?? 'حدث جديد من Finnhub';
$url = $data['url'] ?? null;

$message = "📈 *حدث جديد من Finnhub:*\n\n";
$message .= "*العنوان:* " . $title . "\n";
if ($url) {
    $message .= "\n[رابط التفاصيل]($url)";
}

// إرسال إلى Telegram
$telegram_url = "https://api.telegram.org/bot{$telegram_token}/sendMessage";
$params = [
    'chat_id' => $chat_id,
    'text' => $message,
    'parse_mode' => 'Markdown'
];

$response = file_get_contents($telegram_url . '?' . http_build_query($params));
file_put_contents('log.txt', "📬 تم الإرسال إلى Telegram: {$response}\n", FILE_APPEND);

// نجاح
http_response_code(200);
echo "OK";

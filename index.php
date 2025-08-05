<?php
// ملف: finnhub_webhook.php

// إعدادات Telegram
$telegram_token = '8454842225:AAHRWxEtU1f9patcvyhkZMeYA5kLTh0itQs';
$chat_id = '6059267756'; // الرقم الذي حصلت عليه من getUpdates

// استقبال البيانات من Webhook
$secret = $_SERVER['HTTP_X_FINNHUB_SECRET'] ?? '';
$expected_secret = 'd296r4hr01qhoena9grg'; // كما في لوحة Finnhub

if ($secret !== $expected_secret) {
    http_response_code(403);
    exit('Invalid secret');
}

$data = json_decode(file_get_contents('php://input'), true);

// استخراج العنوان من الخبر (حسب نوع الحدث المرسل)
$title = $data['headline'] ?? 'حدث جديد من Finnhub';
$url = $data['url'] ?? null;

$message = "📈 *حدث جديد من Finnhub:*\n\n";
$message .= "*العنوان:* " . $title . "\n";
if ($url) {
    $message .= "[رابط التفاصيل]($url)";
}

// إرسال إلى Telegram
$telegram_url = "https://api.telegram.org/bot{$telegram_token}/sendMessage";

$params = [
    'chat_id' => $chat_id,
    'text' => $message,
    'parse_mode' => 'Markdown'
];

file_get_contents($telegram_url . '?' . http_build_query($params));

// رد إيجابي
http_response_code(200);
echo "OK";

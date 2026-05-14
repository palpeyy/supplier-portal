<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$to = $argv[1] ?? 'naufalaulia05@gmail.com';

try {
    Illuminate\Support\Facades\Mail::raw(
        'Test email dari Supplier Portal (SMTP check).',
        function ($message) use ($to) {
            $message->to($to)->subject('Test Email SMTP - Supplier Portal');
        }
    );

    echo "SENT to {$to}" . PHP_EOL;
} catch (Throwable $e) {
    echo "FAILED: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}


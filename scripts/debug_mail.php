<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "mail.default=" . config('mail.default') . PHP_EOL;
echo "mail.smtp.host=" . config('mail.mailers.smtp.host') . PHP_EOL;
echo "mail.smtp.port=" . config('mail.mailers.smtp.port') . PHP_EOL;
echo "mail.from.address=" . config('mail.from.address') . PHP_EOL;

$email = 'naufalaulia05@gmail.com';
$user = App\Models\User::where('email', $email)->with('role')->first();

if (!$user) {
    echo "user_not_found email={$email}" . PHP_EOL;
    exit(0);
}

echo "user_id={$user->id}" . PHP_EOL;
echo "user_email={$user->email}" . PHP_EOL;
echo "user_role=" . ($user->role->name ?? 'null') . PHP_EOL;

$financeEmails = App\Models\User::whereHas('role', function ($q) {
    $q->where('name', 'Finance');
})->pluck('email');

echo "finance_count=" . $financeEmails->count() . PHP_EOL;
foreach ($financeEmails as $financeEmail) {
    echo "finance_email={$financeEmail}" . PHP_EOL;
}


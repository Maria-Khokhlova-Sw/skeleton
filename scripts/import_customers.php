<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (json_decode(file_get_contents(__DIR__ . '/database/data/Заказчики.json'), true) as $c) {
    DB::table('customers')->insert([
        'external_id' => $c['id'],
        'name'        => $c['name'],
        'inn'         => $c['inn'] ?: null,
        'address'     => $c['address'] ?? null,
        'phone'       => $c['phone'] ?? null,
        'salesman'    => $c['salesman'] ?? null,
        'buyer'       => $c['buyer'] ?? null,
    ]);
}

echo "Все клиенты успешно добавлены!\n";

<?php

use Coinpush\Config;
use Coinpush\Client;

$client = new Client(new Config);
$response = $client->create('btc', [
    'amount' => 200000, // Satoshis to charge (0.0002 * 100000000).
    'output_address' => '142ZaKhcv68Yepqqu5TuQ88kLbBVxcVeRW',
]);
$results = $response['results'];

var_dump($results);

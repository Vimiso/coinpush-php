## Description

Enable cryptocurrency payments in your web applications or business.

This PHP SDK interacts with the [Coinpush.io API](http://coinpush.test/docs/api) handling the creation and monitoring of cryptocurrency payments.

## Contents

* [Prerequisites](#prerequisites)
* [Installation](#installation)
* [Usage](#usage)
    * [Currencies](#currencies)
    * [Initialisation](#initialisation)
    * [Testnet](#testnet)
    * [Approach A](#approach-a)
        * [Payment Creation](#payment-creation)
        * [Payment Monitoring](#payment-monitoring)
    * [Approach B](#approach-b)
        * [Charge Tokens](#charge-tokens)
        * [Charge Monitoring](#charge-monitoring)
    * [Exceptions](#exceptions)
    * [Response Status Codes](#response-status-codes)
* [Links](#links)

## Prerequisites

* PHP >= 7.0.0
* Composer

## Installation

```
composer require vimiso/coinpush-php:1.*
```

## Usage

### Currencies

Coinpush currently supports: `btc`, `bch` and `ltc`.

Be sure to check out Coinpush's [supported cryptocurrencies](https://coinpush.io/docs/api#currencies) for an up-to-date list, as well as fee information.

### Initialisation

To get started, inject the `Config` class into the `Client` constructor:

```php
<?php

use Coinpush\Config;
use Coinpush\Client;
use Coinpush\Exceptions\RequestException;

$client = new Client(new Config);
```

### Testnet

Want to request the [Coinpush.io Testnet](https://coinpush.io/api/testnet)? Enable it like so:

```php
$config = (new Config)->useTestnet();
$client = new Client($config);
```

IMPORTANT: Please do not send real payments to any addresses created on the Testnet, as our systems do not monitor them automatically.

## Approach A

This enables payments on a per-cryptocurrency basis.

### Payment Creation

To create a new payment address, use the following method:

```php
$currency = 'btc'; // The cryptocurrency to charge in.
$response = $client->create($currency, [
    'amount' => 200000, // Satoshis to charge (0.0002 * 100000000).
    'output_address' => 'YOUR_BTC_OUTPUT_ADDRESS',
    // 'callback_url' => 'OPTIONAL_WEBHOOK_NOTIFICATION_URL',
]);

$results = $response['results'];
$label = $results['address']['label'];
$depositAddress = $results['address']['deposit_address'];
```

For more information on the `create` resource visit: [Coinpush.io API Payment Creation](https://coinpush.io/docs/api#creation).

### Payment Monitoring

#### Webhooks
Webhook notifications are sent if a `callback_url` is supplied when creating a payment - read more about webhooks on the official docs: [Coinpush.io API Payment Monitoring](https://coinpush.io/docs/api#monitoring).

#### Manually
You can use a payment's address `label`, made when creating a payment, to inspect its statuses:

```php
$label = 'ADDRESS_LABEL_FROM_PAYMENT_CREATION';
$response = $client->statuses($label);
$results = $response['results'];

// Collect only the statuses from the array.
$achieved = array_column($results['statuses'], 'status');

// The payment status to check for.
$status = 'balance_sufficient';

// Create boolean stating whether the status was met.
$paymentWasSuccessful = in_array($status, $achieved);
```

Statuses indicate changes in a payment's lifespan. To discover which statuses you can check for, see: [Coinpush.io API Statuses](https://coinpush.io/docs/api#statuses).

## Approach B

This approach enables payments via one, or more, cryptocurrencies based on a fiat currency and amount. It makes use of charge tokens, which are used in conjunction with the Javascript [Charge UI plugin](https://github.com/Vimiso/coinpush-charge-ui).

### Charge Tokens

```php
$fiat = 'usd'; // The fiat currency to charge in.
$response = $client->charge($fiat, [
    'amount' => 12.45,
    'accept' => [
        'btc' => 'YOUR_BTC_OUTPUT_ADDRESS',
        'bch' => 'YOUR_BCH_OUTPUT_ADDRESS',
        'ltc' => 'YOUR_LTC_OUTPUT_ADDRESS',
    ],
]);

$results = $response['results'];
$token = $results['charge']['token'];

// Use $token on your template with the Charge UI plugin.
```

Read more about charge tokens here: [Coinpush.io API Charge Tokens](https://coinpush.io/docs/api#charge-tokens).

### Charge Monitoring

#### Manually

To monitor a charge payment you must inspect its statuses:

```php
$token = 'CHARGE_TOKEN_FROM_CHARGE_CREATION';
$response = $client->chargeView($token);
$results = $response['results'];

// Collect only the statuses from the array.
$achieved = array_column($results['statuses'], 'status');

// The payment status to check for.
$status = 'balance_sufficient';

// Create boolean stating whether the status was met.
$paymentWasSuccessful = in_array($status, $achieved);
```

Read more about charge monitoring here: [Coinpush.io API Charge Monitoring](https://coinpush.io/docs/api#charge-monitoring).

### Exceptions

We recommend handling exceptions in the following manner:

```php
<?php

use Coinpush\Config;
use Coinpush\Client;
use Coinpush\Exceptions\RequestException;

try {
    // Make request here...
} catch (RequestException $e) {
    $response = $e->getResponse();
    $statusCode = $e->getStatusCode();

    // Handle request exceptions as your wish...

    throw $e;
} catch (\Throwable $e) {
    // Handle all other exceptions as your wish...

    throw $e;
}
```

### Response Status Codes

Coinpush always responds with meaningful HTTP status codes, look out for these:

| Code | Description |
| ---- |-------------|
| 200  | The request was successful. |
| 201  | The request was successful and a new resource was created. |
| 400  | The request was not validated or formatted properly. |
| 404  | The given input or resource was not found. |
| 405  | The request method was not supported. |
| 429  | You exceeded the given [rate limit](https://coinpush.io/docs/api#limiting). |
| 500  | The API experienced an internal server error. |
| 503  | The API is down for maintenance. |

## Links

* [Coinpush.io API Docs](https://coinpush.io/docs/api)
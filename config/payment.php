<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VNPay Configuration
    |--------------------------------------------------------------------------
    */
    'vnpay' => [
        'tmn_code'    => env('VNPAY_TMN_CODE', ''),
        'hash_secret' => env('VNPAY_HASH_SECRET', ''),
        'url'         => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url'  => env('APP_URL') . '/webhook/vnpay/return',
        'ipn_url'     => env('APP_URL') . '/webhook/vnpay/ipn',
    ],

    /*
    |--------------------------------------------------------------------------
    | MoMo Configuration
    |--------------------------------------------------------------------------
    */
    'momo' => [
        'partner_code' => env('MOMO_PARTNER_CODE', ''),
        'access_key'   => env('MOMO_ACCESS_KEY', ''),
        'secret_key'   => env('MOMO_SECRET_KEY', ''),
        'endpoint'     => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'return_url'   => env('APP_URL') . '/webhook/momo/return',
        'ipn_url'      => env('APP_URL') . '/webhook/momo/ipn',
    ],

    /*
    |--------------------------------------------------------------------------
    | ZaloPay Configuration
    |--------------------------------------------------------------------------
    */
    'zalopay' => [
        'app_id'     => env('ZALOPAY_APP_ID', ''),
        'key1'       => env('ZALOPAY_KEY1', ''),
        'key2'       => env('ZALOPAY_KEY2', ''),
        'endpoint'   => env('ZALOPAY_ENDPOINT', 'https://sb-openapi.zalopay.vn/v2/create'),
        'return_url' => env('APP_URL') . '/webhook/zalopay/return',
        'ipn_url'    => env('APP_URL') . '/webhook/zalopay/callback',
    ],

];

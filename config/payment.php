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
    | ZaloPay Configuration (direct — giữ lại cho tương lai nếu cần)
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

    /*
    |--------------------------------------------------------------------------
    | PayOS Configuration
    | Đăng ký tại: https://my.payos.vn
    | Hỗ trợ: ZaloPay, MoMo, ATM, Visa/Mastercard, chuyển khoản
    |--------------------------------------------------------------------------
    */
    'payos' => [
        'client_id'    => env('PAYOS_CLIENT_ID', ''),
        'api_key'      => env('PAYOS_API_KEY', ''),
        'checksum_key' => env('PAYOS_CHECKSUM_KEY', ''),
        'base_url'     => env('PAYOS_BASE_URL', 'https://api-merchant.payos.vn'),
        'return_url'   => env('APP_URL') . '/webhook/payos/return',
        'cancel_url'   => env('APP_URL') . '/webhook/payos/cancel',
    ],

];

<?php

namespace App\Services;

use Illuminate\Support\Str;

class VNPayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $url;
    private string $returnUrl;
    private string $ipnUrl;

    public function __construct()
    {
        $this->tmnCode    = config('payment.vnpay.tmn_code', '');
        $this->hashSecret = config('payment.vnpay.hash_secret', '');
        $this->url        = config('payment.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $this->returnUrl  = config('payment.vnpay.return_url', '');
        $this->ipnUrl     = config('payment.vnpay.ipn_url', '');
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl(array $data): string
    {
        $vnpParams = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => (int) ($data['amount'] * 100), // VNPay tính đơn vị VND * 100
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $data['order_code'],
            'vnp_OrderInfo'  => 'Dat phong #' . $data['order_code'] . ' - StayGo',
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpnUrl'     => $this->ipnUrl,
            'vnp_IpAddr'     => $data['ip'] ?? '127.0.0.1',
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_ExpireDate' => now()->addMinutes(30)->format('YmdHis'),
        ];

        if (!empty($data['bank_code'])) {
            $vnpParams['vnp_BankCode'] = $data['bank_code'];
        }

        ksort($vnpParams);

        $queryString = http_build_query($vnpParams, '', '&', PHP_QUERY_RFC3986);
        $vnpParams['vnp_SecureHash'] = hash_hmac('sha512', $queryString, $this->hashSecret);

        return $this->url . '?' . $queryString . '&vnp_SecureHash=' . $vnpParams['vnp_SecureHash'];
    }

    /**
     * Xác minh callback từ VNPay (Return URL / IPN)
     */
    public function verifyCallback(array $inputData): bool
    {
        $vnpSecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $queryString = http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);
        $secureHash  = hash_hmac('sha512', $queryString, $this->hashSecret);

        return hash_equals($secureHash, $vnpSecureHash);
    }

    /**
     * Kiểm tra giao dịch thành công
     */
    public function isSuccess(array $data): bool
    {
        return ($data['vnp_ResponseCode'] ?? '') === '00'
            && ($data['vnp_TransactionStatus'] ?? '') === '00';
    }

    /**
     * Lấy order_code từ callback
     */
    public function getOrderCode(array $data): ?string
    {
        return $data['vnp_TxnRef'] ?? null;
    }

    /**
     * Lấy transaction reference
     */
    public function getTransactionNo(array $data): ?string
    {
        return $data['vnp_TransactionNo'] ?? null;
    }
}

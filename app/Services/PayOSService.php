<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayOSService
{
    private string $clientId;
    private string $apiKey;
    private string $checksumKey;
    private string $baseUrl;
    private string $returnUrl;
    private string $cancelUrl;

    public function __construct()
    {
        $this->clientId    = config('payment.payos.client_id', '');
        $this->apiKey      = config('payment.payos.api_key', '');
        $this->checksumKey = config('payment.payos.checksum_key', '');
        $this->baseUrl     = config('payment.payos.base_url', 'https://api-merchant.payos.vn');
        $this->returnUrl   = config('payment.payos.return_url', '');
        $this->cancelUrl   = config('payment.payos.cancel_url', '');
    }

    /**
     * Tạo payment link. Trả về URL redirect đến trang thanh toán PayOS.
     * $data: ['booking_id', 'order_code', 'amount', 'buyer_name', 'buyer_email', 'buyer_phone']
     */
    public function createPaymentLink(array $data): array
    {
        // PayOS yêu cầu orderCode là số nguyên — dùng booking_id
        $orderCode   = (int) $data['booking_id'];
        $amount      = (int) $data['amount'];
        // Description: tối đa 25 ký tự, không dấu tiếng Việt
        $description = 'Don ' . $data['order_code'];

        $signature = $this->createSignature($amount, $orderCode, $description);

        $payload = [
            'orderCode'   => $orderCode,
            'amount'      => $amount,
            'description' => $description,
            'returnUrl'   => $this->returnUrl,
            'cancelUrl'   => $this->cancelUrl,
            'signature'   => $signature,
        ];

        if (!empty($data['buyer_name']))  $payload['buyerName']  = $data['buyer_name'];
        if (!empty($data['buyer_email'])) $payload['buyerEmail'] = $data['buyer_email'];
        if (!empty($data['buyer_phone'])) $payload['buyerPhone'] = $data['buyer_phone'];

        $response = Http::timeout(30)
            ->withHeaders([
                'x-client-id' => $this->clientId,
                'x-api-key'   => $this->apiKey,
            ])
            ->post($this->baseUrl . '/v2/payment-requests', $payload)
            ->json();

        return $response ?? [];
    }

    /**
     * Tạo chữ ký cho create payment link.
     * Format: amount=X&cancelUrl=X&description=X&orderCode=X&returnUrl=X (sorted A-Z)
     */
    private function createSignature(int $amount, int $orderCode, string $description): string
    {
        $data = [
            'amount'      => $amount,
            'cancelUrl'   => $this->cancelUrl,
            'description' => $description,
            'orderCode'   => $orderCode,
            'returnUrl'   => $this->returnUrl,
        ];
        ksort($data);
        $queryString = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        return hash_hmac('sha256', $queryString, $this->checksumKey);
    }

    /**
     * Xác minh chữ ký webhook từ PayOS.
     * PayOS gửi POST với {"code", "desc", "success", "data": {...}, "signature"}
     */
    public function verifyWebhookSignature(array $webhookData): bool
    {
        $signature = $webhookData['signature'] ?? '';
        $data      = $webhookData['data'] ?? [];

        if (empty($signature) || empty($data)) {
            return false;
        }

        // null → '' để match cách PayOS tính signature
        $data = array_map(fn($v) => $v ?? '', $data);
        ksort($data);

        // PayOS dùng key=value nối & trực tiếp, không URL-encode
        $parts = [];
        foreach ($data as $k => $v) {
            $parts[] = $k . '=' . $v;
        }
        $queryString       = implode('&', $parts);
        $expectedSignature = hash_hmac('sha256', $queryString, $this->checksumKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Kiểm tra giao dịch thành công từ webhook data.
     */
    public function isWebhookSuccess(array $webhookData): bool
    {
        return ($webhookData['code'] ?? '') === '00'
            && ($webhookData['success'] ?? false) === true;
    }

    /**
     * Lấy booking_id từ webhook data (PayOS orderCode = booking_id).
     */
    public function getBookingIdFromWebhook(array $webhookData): ?int
    {
        $orderCode = $webhookData['data']['orderCode'] ?? null;
        return $orderCode !== null ? (int) $orderCode : null;
    }

    /**
     * Lấy transaction reference từ webhook data.
     */
    public function getTransactionRef(array $webhookData): ?string
    {
        return $webhookData['data']['reference'] ?? $webhookData['data']['paymentLinkId'] ?? null;
    }

    /**
     * Lấy booking_id từ return/cancel URL params.
     */
    public function getBookingIdFromReturn(array $params): ?int
    {
        $orderCode = $params['orderCode'] ?? null;
        return $orderCode !== null ? (int) $orderCode : null;
    }

    /**
     * Kiểm tra return URL là thanh toán thành công.
     */
    public function isReturnSuccess(array $params): bool
    {
        return ($params['code'] ?? '') === '00'
            && ($params['status'] ?? '') === 'PAID'
            && ($params['cancel'] ?? 'true') !== 'true';
    }
}

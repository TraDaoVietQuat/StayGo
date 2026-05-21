<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZalopayService
{
    private int    $appId;
    private string $key1;
    private string $key2;
    private string $endpoint;
    private string $returnUrl;
    private string $ipnUrl;

    public function __construct()
    {
        $this->appId     = (int) config('payment.zalopay.app_id', 0);
        $this->key1      = config('payment.zalopay.key1', '');
        $this->key2      = config('payment.zalopay.key2', '');
        $this->endpoint  = config('payment.zalopay.endpoint', 'https://sb-openapi.zalopay.vn/v2/create');
        $this->returnUrl = config('payment.zalopay.return_url', '');
        $this->ipnUrl    = config('payment.zalopay.ipn_url', '');
    }

    /**
     * Tạo yêu cầu thanh toán ZaloPay, trả về response từ API (có order_url).
     */
    public function createPayment(array $data): array
    {
        $appTransId = date('ymd') . '_' . $data['order_code'];
        $appTime    = (int) round(microtime(true) * 1000);
        $amount     = (int) $data['amount'];
        $appUser    = (string) ($data['user_id'] ?? 'guest');

        $embedData = json_encode([
            'redirecturl' => $this->returnUrl . '?order_code=' . $data['order_code'],
        ]);
        $item = json_encode([]);

        // mac = HMAC-SHA256(app_id|app_trans_id|app_user|amount|app_time|embed_data|item, key1)
        $rawHash = implode('|', [
            $this->appId,
            $appTransId,
            $appUser,
            $amount,
            $appTime,
            $embedData,
            $item,
        ]);
        $mac = hash_hmac('sha256', $rawHash, $this->key1);

        $payload = [
            'app_id'       => $this->appId,
            'app_trans_id' => $appTransId,
            'app_user'     => $appUser,
            'app_time'     => $appTime,
            'amount'       => $amount,
            'item'         => $item,
            'embed_data'   => $embedData,
            'description'  => 'Dat phong #' . $data['order_code'] . ' - StayGo',
            'bank_code'    => '',
            'callback_url' => $this->ipnUrl,
            'mac'          => $mac,
        ];

        $response = Http::timeout(30)->post($this->endpoint, $payload)->json();

        return $response ?? [];
    }

    /**
     * Xác minh chữ ký IPN từ ZaloPay (key2).
     * ZaloPay gửi POST: {"data": "<json_string>", "mac": "<hmac>"}
     */
    public function verifyCallback(string $data, string $mac): bool
    {
        $expected = hash_hmac('sha256', $data, $this->key2);
        return hash_equals($expected, $mac);
    }

    /**
     * Xác minh checksum trên return URL (key1).
     * Params: appid|apptransid|pmcid|bankcode|amount|discountamount|status
     */
    public function verifyReturn(array $params): bool
    {
        $message = implode('|', [
            $params['appid']          ?? '',
            $params['apptransid']     ?? '',
            $params['pmcid']          ?? '',
            $params['bankcode']       ?? '',
            $params['amount']         ?? '',
            $params['discountamount'] ?? '',
            $params['status']         ?? '',
        ]);
        $expected = hash_hmac('sha256', $message, $this->key1);
        return hash_equals($expected, $params['checksum'] ?? '');
    }

    /**
     * Kiểm tra giao dịch thành công.
     * IPN: status = 1, Return URL: status = 1
     */
    public function isSuccess(int $status): bool
    {
        return $status === 1;
    }

    /**
     * Lấy order_code từ app_trans_id (format: yymmdd_ORDERCODE).
     */
    public function getOrderCode(string $appTransId): ?string
    {
        $parts = explode('_', $appTransId, 2);
        return count($parts) === 2 ? $parts[1] : null;
    }

    /**
     * Parse data JSON từ IPN callback.
     */
    public function parseCallbackData(string $dataJson): array
    {
        return json_decode($dataJson, true) ?? [];
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MoMoService
{
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $endpoint;
    private string $returnUrl;
    private string $ipnUrl;

    public function __construct()
    {
        $this->partnerCode = config('payment.momo.partner_code', '');
        $this->accessKey   = config('payment.momo.access_key', '');
        $this->secretKey   = config('payment.momo.secret_key', '');
        $this->endpoint    = config('payment.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $this->returnUrl   = config('payment.momo.return_url', '');
        $this->ipnUrl      = config('payment.momo.ipn_url', '');
    }

    /**
     * Tạo yêu cầu thanh toán MoMo
     */
    public function createPayment(array $data): array
    {
        $requestId   = $data['order_code'] . '_' . time();
        $orderId     = $data['order_code'];
        $amount      = (int) $data['amount'];
        $orderInfo   = 'Dat phong #' . $orderId . ' - StayGo';
        $requestType = 'payWithMethod';
        $extraData   = '';
        $lang        = 'vi';

        $rawHash = "accessKey={$this->accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$this->ipnUrl}"
            . "&orderId={$orderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$this->partnerCode}"
            . "&redirectUrl={$this->returnUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $payload = [
            'partnerCode' => $this->partnerCode,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $this->returnUrl,
            'ipnUrl'      => $this->ipnUrl,
            'lang'        => $lang,
            'requestType' => $requestType,
            'autoCapture' => true,
            'extraData'   => $extraData,
            'signature'   => $signature,
        ];

        $response = Http::timeout(30)
            ->post($this->endpoint, $payload)
            ->json();

        return $response ?? [];
    }

    /**
     * Xác minh chữ ký IPN từ MoMo
     */
    public function verifySignature(array $data): bool
    {
        $rawHash = "accessKey={$this->accessKey}"
            . "&amount={$data['amount']}"
            . "&extraData={$data['extraData']}"
            . "&message={$data['message']}"
            . "&orderId={$data['orderId']}"
            . "&orderInfo={$data['orderInfo']}"
            . "&orderType={$data['orderType']}"
            . "&partnerCode={$data['partnerCode']}"
            . "&payType={$data['payType']}"
            . "&requestId={$data['requestId']}"
            . "&responseTime={$data['responseTime']}"
            . "&resultCode={$data['resultCode']}"
            . "&transId={$data['transId']}";

        $expectedSignature = hash_hmac('sha256', $rawHash, $this->secretKey);
        return hash_equals($expectedSignature, $data['signature'] ?? '');
    }

    /**
     * Kiểm tra giao dịch thành công
     */
    public function isSuccess(array $data): bool
    {
        return (int)($data['resultCode'] ?? -1) === 0;
    }

    /**
     * Lấy order_code từ callback
     */
    public function getOrderCode(array $data): ?string
    {
        return $data['orderId'] ?? null;
    }

    /**
     * Lấy transaction id
     */
    public function getTransactionId(array $data): ?string
    {
        return (string)($data['transId'] ?? null);
    }
}

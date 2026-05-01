<?php

namespace App\Services;

use App\Models\Booking;

/**
 * Tạo hoá đơn PDF dạng HTML-to-PDF đơn giản bằng DomPDF (nếu có)
 * hoặc trả về HTML stream nếu chưa cài DomPDF.
 */
class InvoiceService
{
    public function generate(Booking $booking): string
    {
        $html = $this->buildHtml($booking);

        // Dùng DomPDF nếu đã cài (composer require dompdf/dompdf)
        if (class_exists(\Dompdf\Dompdf::class)) {
            $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => false]);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            return $dompdf->output();
        }

        // Fallback: trả HTML (browser có thể in thành PDF)
        return $html;
    }

    public function contentType(): string
    {
        return class_exists(\Dompdf\Dompdf::class) ? 'application/pdf' : 'text/html';
    }

    private function buildHtml(Booking $booking): string
    {
        $nights = $booking->check_in && $booking->check_out
            ? $booking->check_in->diffInDays($booking->check_out)
            : 0;

        $methods = [
            'hotel'         => 'Thanh toán tại khách sạn',
            'momo'          => 'Ví MoMo',
            'vnpay'         => 'VNPay',
            'bank'          => 'Chuyển khoản ngân hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'zalopay'       => 'ZaloPay',
            'cod'           => 'Thanh toán khi nhận phòng',
        ];

        $method       = $methods[$booking->payment_method] ?? strtoupper($booking->payment_method);
        $hotelName    = $booking->room?->hotel?->name ?? '—';
        $hotelAddress = $booking->room?->hotel?->address ?? '—';
        $roomName     = $booking->room?->room_name ?? '—';
        $checkIn      = $booking->check_in?->format('d/m/Y') ?? '—';
        $checkOut     = $booking->check_out?->format('d/m/Y') ?? '—';
        $createdAt    = $booking->created_at?->format('d/m/Y H:i') ?? '—';
        $total        = number_format($booking->total_price, 0, ',', '.') . 'đ';

        $discountRow = '';
        if ($booking->discount_amount > 0) {
            $discountRow = '<tr><td>Giảm giá (' . $booking->discount_code . ')</td>'
                . '<td style="color:#e91e8c">-' . number_format($booking->discount_amount, 0, ',', '.') . 'đ</td></tr>';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<style>
  * { box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 13px; color: #1a202c; margin: 0; padding: 24px; }
  .header { text-align: center; border-bottom: 2px solid #e91e8c; padding-bottom: 16px; margin-bottom: 20px; }
  .header h1 { color: #e91e8c; font-size: 22px; margin: 0 0 4px; }
  .header p { color: #94a3b8; font-size: 12px; margin: 0; }
  .badge { display: inline-block; background: #fdf2f8; color: #e91e8c; border: 1px solid #e91e8c; border-radius: 4px; padding: 2px 10px; font-size: 11px; font-weight: 700; }
  .section { margin-bottom: 18px; }
  .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: #94a3b8; margin-bottom: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px; }
  table { width: 100%; border-collapse: collapse; }
  td { padding: 6px 4px; font-size: 13px; }
  td:last-child { text-align: right; font-weight: 600; }
  .total-row td { font-size: 15px; font-weight: 800; color: #e91e8c; border-top: 2px solid #e91e8c; padding-top: 10px; }
  .footer { margin-top: 32px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; }
  .order-code { font-size: 20px; font-weight: 800; color: #e91e8c; letter-spacing: 2px; }
</style>
</head>
<body>
  <div class="header">
    <h1>🏨 StayGo</h1>
    <p>146 Nguyễn Văn Cừ, TP. Kon Tum &nbsp;|&nbsp; support@staygo.vn &nbsp;|&nbsp; 037 384 8395</p>
    <p style="margin-top:8px;font-size:16px;font-weight:700;color:#1a202c;">HOÁ ĐƠN ĐẶT PHÒNG</p>
  </div>

  <div class="section">
    <div class="section-title">Thông tin đơn hàng</div>
    <table>
      <tr><td style="color:#718096">Mã đặt phòng</td><td><span class="order-code">{$booking->order_code}</span></td></tr>
      <tr><td style="color:#718096">Ngày đặt</td><td>{$createdAt}</td></tr>
      <tr><td style="color:#718096">Trạng thái</td><td><span class="badge">Đã xác nhận</span></td></tr>
    </table>
  </div>

  <div class="section">
    <div class="section-title">Thông tin khách hàng</div>
    <table>
      <tr><td style="color:#718096">Họ tên</td><td>{$booking->full_name}</td></tr>
      <tr><td style="color:#718096">Email</td><td>{$booking->email}</td></tr>
      <tr><td style="color:#718096">Điện thoại</td><td>{$booking->phone}</td></tr>
    </table>
  </div>

  <div class="section">
    <div class="section-title">Thông tin lưu trú</div>
    <table>
      <tr><td style="color:#718096">Khách sạn</td><td>{$hotelName}</td></tr>
      <tr><td style="color:#718096">Địa chỉ</td><td>{$hotelAddress}</td></tr>
      <tr><td style="color:#718096">Loại phòng</td><td>{$roomName}</td></tr>
      <tr><td style="color:#718096">Check-in</td><td>{$checkIn}</td></tr>
      <tr><td style="color:#718096">Check-out</td><td>{$checkOut}</td></tr>
      <tr><td style="color:#718096">Số đêm</td><td>{$nights} đêm</td></tr>
      <tr><td style="color:#718096">Phương thức TT</td><td>{$method}</td></tr>
    </table>
  </div>

  <div class="section">
    <div class="section-title">Chi tiết thanh toán</div>
    <table>
      <tr><td>Tiền phòng ({$nights} đêm × {$roomName})</td><td>{$total}</td></tr>
      {$discountRow}
      <tr class="total-row"><td>TỔNG CỘNG</td><td>{$total}</td></tr>
    </table>
  </div>

  <div class="footer">
    <p>Cảm ơn bạn đã chọn StayGo! Chúc bạn có kỳ nghỉ tuyệt vời.</p>
    <p>© {$this->year()} StayGo. Hoá đơn này được tạo tự động, có giá trị xuất trình khi check-in.</p>
  </div>
</body>
</html>
HTML;
    }

    private function year(): string
    {
        return date('Y');
    }
}

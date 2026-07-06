<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .container { padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2d6a4f; padding-bottom: 20px; }
        .header h1 { font-size: 22px; color: #2d6a4f; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; }
        .invoice-info { margin-bottom: 25px; }
        .invoice-info table { width: 100%; }
        .invoice-info td { padding: 3px 0; vertical-align: top; }
        .invoice-info .label { font-weight: bold; width: 150px; }
        .section-title { font-size: 13px; font-weight: bold; color: #2d6a4f; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .guest-info { margin-bottom: 25px; }
        .guest-info table { width: 100%; }
        .guest-info td { padding: 3px 0; }
        .guest-info .label { font-weight: bold; width: 150px; }
        .price-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .price-table th, .price-table td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #eee; }
        .price-table th { background-color: #f8f9fa; font-weight: bold; border-bottom: 2px solid #ddd; }
        .price-table .text-right { text-align: right; }
        .price-table .total-row { font-weight: bold; border-top: 2px solid #2d6a4f; background-color: #f0fdf4; }
        .price-table .discount-row { color: #dc2626; }
        .payment-info { margin-bottom: 25px; }
        .payment-info table { width: 100%; }
        .payment-info td { padding: 3px 0; }
        .payment-info .label { font-weight: bold; width: 150px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>Penginapan Kelapa Sawit</h1>
            <p>Kota Bangun, Kalimantan Timur, Indonesia</p>
        </div>

        {{-- Invoice Info --}}
        <div class="invoice-info">
            <table>
                <tr>
                    <td class="label">No. Invoice</td>
                    <td>: {{ $invoiceNumber }}</td>
                    <td class="label" style="text-align: right;">Tanggal</td>
                    <td style="text-align: right;">: {{ now()->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Kode Booking</td>
                    <td>: {{ $booking->booking_code }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>

        {{-- Guest Info --}}
        <div class="guest-info">
            <div class="section-title">Informasi Tamu</div>
            <table>
                <tr>
                    <td class="label">Nama</td>
                    <td>: {{ $booking->guest_name }}</td>
                </tr>
                @if($booking->guest_email)
                <tr>
                    <td class="label">Email</td>
                    <td>: {{ $booking->guest_email }}</td>
                </tr>
                @endif
                @if($booking->guest_whatsapp)
                <tr>
                    <td class="label">WhatsApp</td>
                    <td>: {{ $booking->guest_whatsapp }}</td>
                </tr>
                @endif
            </table>
        </div>

        {{-- Room & Stay Info --}}
        <div class="guest-info">
            <div class="section-title">Detail Menginap</div>
            <table>
                <tr>
                    <td class="label">Tipe Kamar</td>
                    <td>: {{ $booking->room_type_name_snapshot }}</td>
                </tr>
                <tr>
                    <td class="label">Kamar</td>
                    <td>: {{ $booking->room_name_snapshot }}</td>
                </tr>
                <tr>
                    <td class="label">Check-in</td>
                    <td>: {{ $booking->check_in->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Check-out</td>
                    <td>: {{ $booking->check_out->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Malam</td>
                    <td>: {{ $booking->nights }} malam</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Tamu</td>
                    <td>: {{ $booking->guest_count }} orang</td>
                </tr>
            </table>
        </div>

        {{-- Price Table --}}
        <div class="section-title">Rincian Biaya</div>
        <table class="price-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Harga per malam (Rp{{ number_format($booking->price_per_night_snapshot, 0, ',', '.') }} × {{ $booking->nights }} malam)</td>
                    <td class="text-right">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($booking->promotion_discount > 0)
                <tr class="discount-row">
                    <td>Diskon Promo{{ $booking->promotion_code_snapshot ? ' (' . $booking->promotion_code_snapshot . ')' : '' }}</td>
                    <td class="text-right">- Rp{{ number_format($booking->promotion_discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($booking->points_discount > 0)
                <tr class="discount-row">
                    <td>Potongan Poin ({{ $booking->points_redeemed }} poin)</td>
                    <td class="text-right">- Rp{{ number_format($booking->points_discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total</td>
                    <td class="text-right">Rp{{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Payment Info --}}
        @if($payment)
        <div class="payment-info">
            <div class="section-title">Informasi Pembayaran</div>
            <table>
                <tr>
                    <td class="label">Status</td>
                    <td>: Lunas</td>
                </tr>
                <tr>
                    <td class="label">Metode</td>
                    <td>: {{ $payment->payment_type ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Bayar</td>
                    <td>: {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            </table>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p>Terima kasih telah menginap di Penginapan Kelapa Sawit.</p>
            <p>Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan.</p>
        </div>
    </div>
</body>
</html>

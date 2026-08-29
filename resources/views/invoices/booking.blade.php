<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { margin: 40px 50px; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 13px; 
            color: #333; 
            line-height: 1.5; 
        }
        
        /* Beautiful Watermark */
        .watermark-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            text-align: center;
        }
        .watermark-text {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            color: rgba(45, 106, 79, 0.06);
            font-weight: 900;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 5px;
            border: 6px solid rgba(45, 106, 79, 0.06);
            padding: 20px 40px;
            border-radius: 20px;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #2d6a4f;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo {
            max-width: 120px;
            height: auto;
        }
        .company-details {
            text-align: right;
        }
        .company-details h1 {
            font-size: 24px;
            color: #2d6a4f;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-details p {
            color: #666;
            font-size: 11px;
            line-height: 1.5;
        }
        
        /* Invoice Info */
        .invoice-info-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #1b4332;
            letter-spacing: 2px;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta table {
            display: inline-table;
            text-align: left;
        }
        .invoice-meta td {
            padding: 3px 0;
            font-size: 13px;
        }
        .invoice-meta .label {
            font-weight: bold;
            color: #555;
            padding-right: 20px;
        }

        /* Grid Info */
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 15px 0;
            margin-left: -15px;
            margin-right: -15px;
        }
        .info-grid td {
            vertical-align: top;
            width: 50%;
        }
        .box {
            background-color: #f8f9fa;
            border-top: 3px solid #2d6a4f;
            border-radius: 4px;
            padding: 15px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .box-title {
            font-size: 13px;
            font-weight: bold;
            color: #2d6a4f;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 4px 0;
            font-size: 12px;
        }
        .info-table .label {
            font-weight: bold;
            width: 110px;
            color: #444;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #2d6a4f;
            color: white;
            padding: 12px 14px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .items-table th.text-center { text-align: center; }
        .items-table th.text-right { text-align: right; }
        
        .items-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            color: #333;
        }
        .items-table .text-center { text-align: center; }
        .items-table .text-right { text-align: right; }
        .items-table tbody tr:nth-child(even) td { background-color: #f8fafc; }
        
        .items-table .summary-row td {
            border-bottom: none;
            padding: 8px 14px;
            background-color: #fff !important;
        }
        .items-table .total-row td {
            font-weight: bold;
            font-size: 16px;
            color: #1b4332;
            border-top: 2px solid #2d6a4f;
            background-color: #edf2f7 !important;
            padding: 14px;
        }
        .discount-text { color: #e11d48; }
        
        /* Footer/Signatures */
        .footer-container {
            margin-top: 15px;
        }
        .footer-grid {
            width: 100%;
            page-break-inside: avoid;
        }
        .footer-grid td {
            vertical-align: bottom;
        }
        .payment-status-box {
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            padding: 12px 18px;
            display: inline-block;
            border-radius: 0 4px 4px 0;
        }
        .payment-status-title {
            color: #166534;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .payment-status-detail {
            font-size: 12px;
            color: #15803d;
        }
        .stamp-box {
            text-align: center;
            width: 220px;
            float: right;
        }
        .stamp-box p { margin-bottom: 45px; font-weight: bold; color: #444; }
        .stamp-box .sign-name { 
            border-top: 1px solid #444; 
            padding-top: 8px; 
            font-weight: bold;
            color: #333;
        }
        
        /* Utilities */
        .text-xs { font-size: 11px; color: #777; }
    </style>
</head>
<body>
    {{-- Watermark --}}
    <div class="watermark-container">
        <div class="watermark-text">
            {{ $payment && $payment->status === 'paid' ? 'PAID / LUNAS' : 'CONFIRMED' }}
        </div>
    </div>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" class="logo" alt="Logo">
                @else
                    <h2 style="color: #2d6a4f; font-size: 22px; font-weight: bold; letter-spacing: 1px;">KELAPA SAWIT</h2>
                @endif
            </td>
            <td class="company-details" style="width: 50%;">
                <h1>Penginapan Kelapa Sawit</h1>
                <p>Gunung Kelambu. Sp 2, Kota Bangun Darat,<br>Kalimantan Timur, Indonesia 75561</p>
                <p>Phone: 081350286635 | Web: kelapasawit.com</p>
            </td>
        </tr>
    </table>

    {{-- Invoice Title & Basic Info --}}
    <table class="invoice-info-table">
        <tr>
            <td style="width: 50%; vertical-align: middle;">
                <div class="invoice-title">INVOICE</div>
            </td>
            <td class="invoice-meta" style="width: 50%;">
                <table>
                    <tr><td class="label">No. Invoice</td><td>: <strong>{{ $invoiceNumber }}</strong></td></tr>
                    <tr><td class="label">Tanggal</td><td>: {{ now()->format('d F Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Grid Info (Guest & Booking) --}}
    <table class="info-grid">
        <tr>
            <td>
                <div class="box">
                    <div class="box-title">Ditagihkan Kepada</div>
                    <table class="info-table">
                        <tr><td class="label">Nama Tamu</td><td>: {{ $booking->guest_name }}</td></tr>
                        <tr><td class="label">Email</td><td>: {{ $booking->guest_email ?? '-' }}</td></tr>
                        <tr><td class="label">No. WhatsApp</td><td>: {{ $booking->guest_whatsapp ?? '-' }}</td></tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="box">
                    <div class="box-title">Detail Reservasi</div>
                    <table class="info-table">
                        <tr><td class="label">Kode Booking</td><td>: <strong>{{ $booking->booking_code }}</strong></td></tr>
                        <tr><td class="label">Tipe / Kamar</td><td>: {{ $booking->room_type_name_snapshot }} / {{ $booking->room_name_snapshot }}</td></tr>
                        <tr><td class="label">Check-in</td><td>: {{ $booking->check_in->format('d M Y') }}</td></tr>
                        <tr><td class="label">Check-out</td><td>: {{ $booking->check_out->format('d M Y') }}</td></tr>
                        <tr><td class="label">Durasi</td><td>: {{ $booking->nights }} Malam, {{ $booking->guest_count }} Tamu</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 50%;">Deskripsi Layanan</th>
                <th style="width: 20%;" class="text-center">Kuantitas</th>
                <th class="text-right" style="width: 25%;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            {{-- Room Charge --}}
            <tr>
                <td class="text-center">1</td>
                <td>
                    <strong>Biaya Menginap ({{ $booking->room_type_name_snapshot }})</strong><br>
                    <span class="text-xs">
                        Periode: {{ $booking->check_in->format('d/m/Y') }} s.d {{ $booking->check_out->format('d/m/Y') }}
                    </span>
                </td>
                <td class="text-center">{{ $booking->nights }} Malam</td>
                <td class="text-right">{{ number_format($booking->subtotal, 0, ',', '.') }}</td>
            </tr>

            {{-- Addons --}}
            @php $itemNo = 2; @endphp
            @if($booking->addons->count() > 0)
                @foreach($booking->addons as $ba)
                <tr>
                    <td class="text-center">{{ $itemNo++ }}</td>
                    <td>Layanan Tambahan: {{ $ba->addon->name ?? 'Layanan' }}</td>
                    <td class="text-center">{{ $ba->quantity }}</td>
                    <td class="text-right">{{ number_format($ba->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endif
            
            {{-- Spacer --}}
            <tr><td colspan="4" style="height: 10px; padding: 0; background: #fff !important; border-bottom: none;"></td></tr>

            {{-- Summaries --}}
            @if($booking->promotion_discount > 0)
            <tr class="summary-row">
                <td colspan="3" class="text-right">Diskon Promo {{ $booking->promotion_code_snapshot ? '('.$booking->promotion_code_snapshot.')' : '' }}</td>
                <td class="text-right discount-text">- {{ number_format($booking->promotion_discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            @if($booking->points_discount > 0)
            <tr class="summary-row">
                <td colspan="3" class="text-right">Penukaran Poin ({{ $booking->points_redeemed }} Poin)</td>
                <td class="text-right discount-text">- {{ number_format($booking->points_discount, 0, ',', '.') }}</td>
            </tr>
            @endif

            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL PEMBAYARAN</td>
                <td class="text-right">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Footer & Signatures --}}
    <div class="footer-container">
        <table class="footer-grid">
            <tr>
                <td style="width: 60%;">
                    <div class="payment-status-box">
                        <div class="payment-status-title">
                            {{ $payment ? 'STATUS: LUNAS (PAID)' : 'STATUS: SELESAI' }}
                        </div>
                        @if($payment)
                        <div class="payment-status-detail">
                            Waktu: {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '-' }}
                        </div>
                        @endif
                    </div>
                    <div style="margin-top: 15px; font-size: 12px; color: #777;">
                        * Terimakasih telah mempercayakan akomodasi Anda kepada kami.
                    </div>
                </td>
                <td style="width: 40%;">
                    <div class="stamp-box">
                        <p>Hormat Kami,</p>
                        @if(file_exists(public_path('images/signature.png')))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/signature.png'))) }}" style="height: 50px; margin: 5px 0;">
                        @else
                            <br><br><br><br>
                        @endif
                        <div class="sign-name">Manajemen Kelapa Sawit</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

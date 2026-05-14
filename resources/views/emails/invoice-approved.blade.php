<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Divalidasi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            color: #2d3748;
            padding: 40px 20px;
        }

        .wrapper {
            max-width: 620px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            border-radius: 16px 16px 0 0;
            padding: 36px 40px;
            text-align: center;
        }

        .header .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .header .logo-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.75);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header .check-icon {
            width: 72px;
            height: 72px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto 16px;
        }

        .header h1 {
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.3;
        }

        .header p.subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.85);
            margin-top: 8px;
        }

        /* Body card */
        .card {
            background: #ffffff;
            padding: 36px 40px;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }

        .greeting {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .greeting strong {
            color: #1a202c;
        }

        /* Status badge */
        .status-banner {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 1px solid #6ee7b7;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .status-banner .badge {
            background: #10b981;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-banner .status-text {
            font-size: 14px;
            color: #065f46;
            font-weight: 600;
        }

        /* Detail table */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 14px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .detail-table tr td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid #f0f4f8;
        }

        .detail-table tr:last-child td {
            border-bottom: none;
        }

        .detail-table tr:nth-child(odd) {
            background-color: #f8fafc;
        }

        .detail-table .label {
            color: #718096;
            font-weight: 600;
            width: 42%;
        }

        .detail-table .value {
            color: #1a202c;
            font-weight: 500;
        }

        .detail-table .value .highlight {
            font-weight: 700;
            color: #2563eb;
        }

        /* Info box */
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin-bottom: 28px;
        }

        .info-box p {
            font-size: 14px;
            color: #1e40af;
            line-height: 1.7;
        }

        .info-box strong {
            color: #1e3a8a;
        }

        /* CTA Button */
        .cta-wrapper {
            text-align: center;
            margin: 28px 0;
        }

        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 24px 0;
        }

        /* Footer */
        .footer {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0 0 16px 16px;
            padding: 24px 40px;
            text-align: center;
        }

        .footer p {
            font-size: 12px;
            color: #a0aec0;
            line-height: 1.8;
        }

        .footer .company {
            font-weight: 700;
            color: #718096;
            font-size: 13px;
        }

        .footer .auto-note {
            margin-top: 8px;
            font-size: 11px;
            color: #cbd5e0;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <!-- Header -->
        <div class="header">
            <div class="logo-text">🏢 Supplier Portal</div>
            <div class="logo-sub">Sistem Manajemen Pengadaan</div>

            <div class="check-icon">
                <!-- Checkmark SVG inline -->
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="rgba(255,255,255,0.2)" />
                    <path d="M7 12.5L10.5 16L17 9" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>

            <h1>Invoice Telah Divalidasi!</h1>
            <p class="subtitle">Dokumen siap untuk diproses pembayaran</p>
        </div>

        <!-- Card body -->
        <div class="card">
            <p class="greeting">
                Halo <strong>Tim Finance</strong>,<br><br>
                Kami ingin memberitahukan bahwa dokumen invoice berikut telah berhasil divalidasi oleh tim Admin/Purchasing dan <strong>siap untuk diproses pembayaran</strong>.
            </p>

            <!-- Status banner -->
            <div class="status-banner">
                <span class="badge">✓ Divalidasi</span>
                <span class="status-text">Invoice telah divalidasi Admin dan <strong>siap diproses pembayaran</strong></span>
            </div>

            <!-- Invoice details -->
            <div class="section-title">📄 Detail Invoice & PO</div>
            <table class="detail-table">
                <tr>
                    <td class="label">No. Purchase Order</td>
                    <td class="value">
                        <span class="highlight">
                            {{ $invoice->purchaseOrder->po_number ?? 'N/A' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Supplier</td>
                    <td class="value">{{ $invoice->purchaseOrder->supplier->nama ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal PO</td>
                    <td class="value">
                        {{ $invoice->purchaseOrder->date ? \Carbon\Carbon::parse($invoice->purchaseOrder->date)->translatedFormat('d F Y') : 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Jumlah Item</td>
                    <td class="value">{{ $invoice->purchaseOrder->items->count() }} item</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Validasi</td>
                    <td class="value">
                        <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</strong>
                    </td>
                </tr>
                <tr>
                    <td class="label">Status Invoice</td>
                    <td class="value">
                        <span style="background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:700;">
                            READY FOR PAYMENT
                        </span>
                    </td>
                </tr>
            </table>

            <!-- Items detail -->
            @if($invoice->purchaseOrder->items && $invoice->purchaseOrder->items->count() > 0)
            <div class="section-title" style="margin-top: 28px;">📦 Daftar Item PO</div>
            <table class="detail-table" style="font-size: 13px;">
                <tr style="background-color: #f0f4f8;">
                    <td class="label" style="width: 50%; font-weight: 700; color: #1a202c;">Item</td>
                    <td class="value" style="width: 25%; font-weight: 700; color: #1a202c; text-align: right;">Qty</td>
                    <td class="value" style="width: 25%; font-weight: 700; color: #1a202c; text-align: right;">Harga</td>
                </tr>
                @foreach($invoice->purchaseOrder->items as $item)
                <tr>
                    <td class="label" style="color: #1a202c;">{{ $item->material_code ?? 'N/A' }}</td>
                    <td class="value" style="text-align: right; color: #1a202c;">{{ $item->quantity ?? 0 }}</td>
                    <td class="value" style="text-align: right; color: #1a202c;">Rp {{ number_format($item->price_per_unit ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f0f4f8; border-top: 2px solid #e2e8f0;">
                    <td class="label" style="font-weight: 700; color: #1a202c;">TOTAL</td>
                    <td class="value" style="text-align: right; font-weight: 700; color: #1a202c;">{{ $invoice->purchaseOrder->items->sum('quantity') ?? 0 }}</td>
                    <td class="value" style="text-align: right; font-weight: 700; color: #2563eb;">Rp {{ number_format($invoice->purchaseOrder->items->sum(function($item) { return $item->quantity * $item->price_per_unit; }) ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
            @endif

            <!-- Attachment info -->
            <div class="info-box" style="margin-top: 28px;">
                <p>
                    📎 <strong>File Terlampir:</strong><br>
                    Email ini dilengkapi dengan 3 dokumen lampiran:<br>
                    • Invoice<br>
                    • Surat Jalan<br>
                    • Faktur Pajak
                </p>
            </div>

            <!-- Action required box -->
            <div class="info-box">
                <p>
                    📌 <strong>Tindakan Diperlukan:</strong><br>
                    Silakan login ke Supplier Portal untuk melihat detail lengkap dokumen dan memproses pembayaran sesuai dengan nilai yang tertera pada invoice.
                </p>
            </div>

            <!-- CTA Button -->
            <div class="cta-wrapper">
                <a href="{{ config('app.url') }}/invoices" class="cta-btn">
                    Lihat Detail Invoice →
                </a>
            </div>

            <hr class="divider">

            <p style="font-size:13px; color:#718096; text-align:center; line-height:1.7;">
                Jika Anda memiliki pertanyaan terkait invoice ini, silakan hubungi tim Admin/Purchasing.<br>
                <strong>Harap tidak membalas email ini</strong> karena dikirim secara otomatis oleh sistem.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="company">Supplier Portal — Sistem Manajemen Pengadaan</p>
            <p>Email ini dikirim secara otomatis pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y \p\u\k\u\l H:i') }} WIB.</p>
            <p class="auto-note">© {{ date('Y') }} Supplier Portal. Semua hak dilindungi.</p>
        </div>

    </div>
</body>

</html>
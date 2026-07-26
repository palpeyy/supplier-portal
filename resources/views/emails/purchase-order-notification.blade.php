@php
    $po = $purchaseOrder;
    $isPending = $type === \App\Mail\PurchaseOrderNotificationMail::TYPE_PENDING_APPROVAL;
    $isApproved = $type === \App\Mail\PurchaseOrderNotificationMail::TYPE_APPROVED;
    $isRejected = $type === \App\Mail\PurchaseOrderNotificationMail::TYPE_REJECTED;

    $headerTitle = $isPending
        ? 'Purchase Order Baru'
        : ($isApproved ? 'Purchase Order Disetujui' : 'Purchase Order Ditolak');

    $headerSubtitle = $isPending
        ? 'Menunggu persetujuan Dept. Head'
        : ($isApproved ? 'Silakan konfirmasi di Supplier Portal' : 'Perlu tindakan dari tim Purchasing');

    $greetingName = $recipientName ?? ($isPending ? 'Dept. Head' : ($isApproved ? 'Supplier' : 'Tim Purchasing'));

    $headerGradient = $isRejected
        ? 'linear-gradient(135deg, #7f1d1d 0%, #dc2626 100%)'
        : ($isApproved
            ? 'linear-gradient(135deg, #14532d 0%, #16a34a 100%)'
            : 'linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%)');

    $bannerBg = $isRejected
        ? 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)'
        : ($isApproved
            ? 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)'
            : 'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)');

    $bannerBorder = $isRejected ? '#fca5a5' : ($isApproved ? '#6ee7b7' : '#93c5fd');
    $bannerTextColor = $isRejected ? '#991b1b' : ($isApproved ? '#065f46' : '#1e40af');
    $badgeBg = $isRejected ? '#dc2626' : ($isApproved ? '#10b981' : '#2563eb');
    $badgeLabel = $isPending ? 'Menunggu Approval' : ($isApproved ? 'Disetujui' : 'Ditolak');
    $portalUrl = rtrim(config('app.url'), '/') . '/purchase-order';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $headerTitle }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            color: #2d3748;
            padding: 40px 20px;
        }
        .wrapper { max-width: 620px; margin: 0 auto; }
        .header {
            background: {{ $headerGradient }};
            border-radius: 16px 16px 0 0;
            padding: 36px 40px;
            text-align: center;
        }
        .header .logo-text { font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .header .logo-sub { font-size: 13px; color: rgba(255,255,255,0.75); text-transform: uppercase; letter-spacing: 1px; }
        .header h1 { font-size: 24px; font-weight: 700; color: #fff; margin-top: 16px; }
        .header p.subtitle { font-size: 14px; color: rgba(255,255,255,0.85); margin-top: 8px; }
        .card { background: #fff; padding: 36px 40px; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; }
        .greeting { font-size: 16px; color: #4a5568; margin-bottom: 20px; line-height: 1.6; }
        .greeting strong { color: #1a202c; }
        .status-banner {
            background: {{ $bannerBg }};
            border: 1px solid {{ $bannerBorder }};
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        .status-banner .badge {
            background: {{ $badgeBg }};
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            white-space: nowrap;
            text-transform: uppercase;
        }
        .status-banner .status-text { font-size: 14px; color: {{ $bannerTextColor }}; font-weight: 600; }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 14px;
        }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        .detail-table tr td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #f0f4f8; }
        .detail-table tr:nth-child(odd) { background-color: #f8fafc; }
        .detail-table .label { color: #718096; font-weight: 600; width: 42%; }
        .detail-table .value { color: #1a202c; font-weight: 500; }
        .detail-table .value .highlight { font-weight: 700; color: #2563eb; }
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin-bottom: 28px;
        }
        .info-box p { font-size: 14px; color: #1e40af; line-height: 1.7; }
        .reject-box {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            border-radius: 0 8px 8px 0;
            padding: 16px 20px;
            margin-bottom: 28px;
        }
        .reject-box p { font-size: 14px; color: #991b1b; line-height: 1.7; }
        .cta-wrapper { text-align: center; margin: 28px 0; }
        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
        }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 24px 0; }
        .footer {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0 0 16px 16px;
            padding: 24px 40px;
            text-align: center;
        }
        .footer p { font-size: 12px; color: #a0aec0; line-height: 1.8; }
        .footer .company { font-weight: 700; color: #718096; font-size: 13px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="logo-text">Supplier Portal</div>
            <div class="logo-sub">Sistem Manajemen Pengadaan</div>
            <h1>{{ $headerTitle }}</h1>
            <p class="subtitle">{{ $headerSubtitle }}</p>
        </div>

        <div class="card">
            <p class="greeting">
                Halo <strong>{{ $greetingName }}</strong>,<br><br>
                @if($isPending)
                    Tim Purchasing telah mengunggah Purchase Order baru yang memerlukan <strong>persetujuan Anda</strong> sebagai Dept. Head.
                @elseif($isApproved)
                    Purchase Order berikut telah <strong>disetujui oleh Dept. Head</strong>. Silakan login ke portal untuk mengonfirmasi dan melanjutkan proses pengiriman.
                @else
                    Purchase Order berikut telah <strong>ditolak oleh Dept. Head</strong>. Silakan tinjau keterangan penolakan dan lakukan tindakan yang diperlukan.
                @endif
            </p>

            <div class="status-banner">
                <span class="badge">{{ $badgeLabel }}</span>
                <span class="status-text">
                    @if($isPending)
                        PO #{{ $po->po_number }} menunggu approval
                    @elseif($isApproved)
                        PO #{{ $po->po_number }} siap dikonfirmasi supplier
                    @else
                        PO #{{ $po->po_number }} ditolak
                    @endif
                </span>
            </div>

            @if($isRejected && $po->keterangan)
            <div class="reject-box">
                <p><strong>Alasan penolakan:</strong><br>{{ $po->keterangan }}</p>
            </div>
            @endif

            <div class="section-title">Detail Purchase Order</div>
            <table class="detail-table">
                <tr>
                    <td class="label">No. Purchase Order</td>
                    <td class="value"><span class="highlight">{{ $po->po_number ?? 'N/A' }}</span></td>
                </tr>
                <tr>
                    <td class="label">Supplier</td>
                    <td class="value">{{ $po->supplier->nama ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal PO</td>
                    <td class="value">
                        {{ $po->created_at ? $po->created_at->translatedFormat('d F Y') : 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Jumlah Item</td>
                    <td class="value">{{ $po->items->count() }} item</td>
                </tr>
                @if($po->createdBy)
                <tr>
                    <td class="label">Diupload oleh</td>
                    <td class="value">{{ $po->createdBy->name }} ({{ $po->createdBy->email }})</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Waktu Notifikasi</td>
                    <td class="value"><strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</strong></td>
                </tr>
            </table>

            @if(!$isRejected && $po->pdf_path)
            <div class="info-box">
                <p>File PDF Purchase Order terlampir pada email ini untuk referensi Anda.</p>
            </div>
            @endif

            <div class="cta-wrapper">
                <a href="{{ $portalUrl }}" class="cta-btn">Buka Supplier Portal</a>
            </div>

            <hr class="divider">

            <p style="font-size:13px; color:#718096; text-align:center; line-height:1.7;">
                Email ini dikirim otomatis oleh sistem. <strong>Harap tidak membalas email ini.</strong>
            </p>
        </div>

        <div class="footer">
            <p class="company">Supplier Portal — Sistem Manajemen Pengadaan</p>
            <p>Email otomatis — {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
            <p>© {{ date('Y') }} Supplier Portal</p>
        </div>
    </div>
</body>
</html>

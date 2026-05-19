@php
    use App\Mail\PurchaseOrderNotificationMail;
    use App\Mail\WorkflowNotificationMail;

    $po = $purchaseOrder;
    $sj = $shippingDocument ?? null;
    $inv = $invoice ?? null;

    $isPo = $module === WorkflowNotificationMail::MODULE_PO;
    $isShipping = $module === WorkflowNotificationMail::MODULE_SHIPPING;
    $isInvoice = $module === WorkflowNotificationMail::MODULE_INVOICE;

    $isPositive = in_array($type, [
        PurchaseOrderNotificationMail::TYPE_APPROVED,
        PurchaseOrderNotificationMail::TYPE_SUPPLIER_APPROVED,
        'approved',
        'uploaded',
    ], true);

    $isRejected = in_array($type, [
        PurchaseOrderNotificationMail::TYPE_REJECTED,
        PurchaseOrderNotificationMail::TYPE_SUPPLIER_REJECTED,
        'rejected',
    ], true);

    $isRevised = $type === 'revised';

    $headerTitle = match (true) {
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_PENDING_APPROVAL => 'Purchase Order Baru',
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_APPROVED => 'Purchase Order Disetujui',
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_REJECTED => 'Purchase Order Ditolak',
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_SUPPLIER_APPROVED => 'PO Dikonfirmasi Supplier',
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_SUPPLIER_REJECTED => 'PO Ditolak Supplier',
        $isShipping && $type === 'created' => 'Surat Jalan Baru',
        $isShipping && $type === 'approved' => 'Surat Jalan Disetujui',
        $isShipping && $type === 'rejected' => 'Surat Jalan Ditolak',
        $isShipping && $type === 'revised' => 'Surat Jalan Perlu Revisi',
        $isInvoice && $type === 'uploaded' => 'Dokumen Tagihan Baru',
        $isInvoice && $type === 'approved' => 'Tagihan Divalidasi',
        $isInvoice && $type === 'rejected' => 'Tagihan Ditolak',
        $isInvoice && $type === 'revised' => 'Tagihan Perlu Revisi',
        default => 'Notifikasi Portal',
    };

    $bodyMessage = match (true) {
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_PENDING_APPROVAL => 'Tim Purchasing mengunggah PO baru yang memerlukan <strong>persetujuan Anda</strong> sebagai Dept. Head.',
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_APPROVED => 'PO berikut telah <strong>disetujui Dept. Head</strong>. Silakan login untuk mengonfirmasi.',
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_REJECTED => 'PO berikut telah <strong>ditolak Dept. Head</strong>. Silakan tinjau dan lakukan tindakan.',
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_SUPPLIER_APPROVED => 'Supplier telah <strong>mengonfirmasi PO</strong> dan pesanan sedang diproses.',
        $isPo && $type === PurchaseOrderNotificationMail::TYPE_SUPPLIER_REJECTED => 'Supplier telah <strong>menolak PO</strong>. Silakan tinjau keterangan.',
        $isShipping && $type === 'created' => 'Supplier membuat <strong>surat jalan baru</strong> yang perlu ditinjau tim Purchasing.',
        $isShipping && $type === 'approved' => 'Surat jalan Anda telah <strong>disetujui</strong> oleh tim Purchasing.',
        $isShipping && $type === 'rejected' => 'Surat jalan Anda telah <strong>ditolak</strong> oleh tim Purchasing.',
        $isShipping && $type === 'revised' => 'Surat jalan Anda <strong>perlu direvisi</strong> oleh tim Purchasing.',
        $isInvoice && $type === 'uploaded' => 'Supplier mengunggah <strong>dokumen tagihan</strong> yang perlu divalidasi.',
        $isInvoice && $type === 'approved' => 'Dokumen tagihan Anda telah <strong>divalidasi</strong> dan siap diproses pembayaran.',
        $isInvoice && $type === 'rejected' => 'Dokumen tagihan Anda telah <strong>ditolak</strong>.',
        $isInvoice && $type === 'revised' => 'Dokumen tagihan Anda <strong>perlu direvisi</strong>.',
        default => 'Ada pembaruan pada portal yang memerlukan perhatian Anda.',
    };

    $badgeLabel = match (true) {
        $isRejected => 'Ditolak',
        $isRevised => 'Perlu Revisi',
        $isPositive => 'Disetujui',
        $type === PurchaseOrderNotificationMail::TYPE_PENDING_APPROVAL, $type === 'created', $type === 'uploaded' => 'Menunggu Tindakan',
        default => 'Pembaruan',
    };

    $portalUrl = match (true) {
        $isInvoice => rtrim(config('app.url'), '/') . '/invoices',
        $isShipping => rtrim(config('app.url'), '/') . '/purchase-order',
        default => rtrim(config('app.url'), '/') . '/purchase-order',
    };

    $headerGradient = $isRejected
        ? 'linear-gradient(135deg, #7f1d1d 0%, #dc2626 100%)'
        : ($isPositive
            ? 'linear-gradient(135deg, #14532d 0%, #16a34a 100%)'
            : ($isRevised
                ? 'linear-gradient(135deg, #92400e 0%, #d97706 100%)'
                : 'linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%)'));

    $bannerBg = $isRejected
        ? 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)'
        : ($isPositive
            ? 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)'
            : ($isRevised
                ? 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)'
                : 'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)'));

    $bannerBorder = $isRejected ? '#fca5a5' : ($isPositive ? '#6ee7b7' : ($isRevised ? '#fcd34d' : '#93c5fd'));
    $bannerTextColor = $isRejected ? '#991b1b' : ($isPositive ? '#065f46' : ($isRevised ? '#92400e' : '#1e40af'));
    $badgeBg = $isRejected ? '#dc2626' : ($isPositive ? '#10b981' : ($isRevised ? '#d97706' : '#2563eb'));
    $greetingName = $recipientName ?? 'Pengguna';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $headerTitle }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; color: #2d3748; padding: 40px 20px; }
        .wrapper { max-width: 620px; margin: 0 auto; }
        .header { background: {{ $headerGradient }}; border-radius: 16px 16px 0 0; padding: 36px 40px; text-align: center; }
        .header .logo-text { font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .header .logo-sub { font-size: 13px; color: rgba(255,255,255,0.75); text-transform: uppercase; letter-spacing: 1px; }
        .header h1 { font-size: 24px; font-weight: 700; color: #fff; margin-top: 16px; }
        .header p.subtitle { font-size: 14px; color: rgba(255,255,255,0.85); margin-top: 8px; }
        .card { background: #fff; padding: 36px 40px; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; }
        .greeting { font-size: 16px; color: #4a5568; margin-bottom: 20px; line-height: 1.6; }
        .status-banner { background: {{ $bannerBg }}; border: 1px solid {{ $bannerBorder }}; border-radius: 10px; padding: 16px 20px; margin-bottom: 28px; display: flex; align-items: center; gap: 12px; }
        .status-banner .badge { background: {{ $badgeBg }}; color: #fff; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; }
        .status-banner .status-text { font-size: 14px; color: {{ $bannerTextColor }}; font-weight: 600; }
        .section-title { font-size: 13px; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        .detail-table tr td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #f0f4f8; }
        .detail-table tr:nth-child(odd) { background: #f8fafc; }
        .detail-table .label { color: #718096; font-weight: 600; width: 42%; }
        .detail-table .value { color: #1a202c; font-weight: 500; }
        .detail-table .highlight { font-weight: 700; color: #2563eb; }
        .notes-box { background: #fef2f2; border-left: 4px solid #dc2626; border-radius: 0 8px 8px 0; padding: 16px 20px; margin-bottom: 28px; }
        .notes-box.revise { background: #fffbeb; border-left-color: #d97706; }
        .notes-box p { font-size: 14px; line-height: 1.7; color: #991b1b; }
        .notes-box.revise p { color: #92400e; }
        .info-box { background: #eff6ff; border-left: 4px solid #2563eb; border-radius: 0 8px 8px 0; padding: 16px 20px; margin-bottom: 28px; }
        .info-box p { font-size: 14px; color: #1e40af; line-height: 1.7; }
        .cta-wrapper { text-align: center; margin: 28px 0; }
        .cta-btn { display: inline-block; background: linear-gradient(135deg, #1e3a5f, #2563eb); color: #fff !important; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-size: 15px; font-weight: 700; }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 24px 0; }
        .footer { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0 0 16px 16px; padding: 24px 40px; text-align: center; }
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
            <p class="subtitle">PO #{{ $po->po_number ?? 'N/A' }}</p>
        </div>

        <div class="card">
            <p class="greeting">Halo <strong>{{ $greetingName }}</strong>,<br><br>{!! $bodyMessage !!}</p>

            <div class="status-banner">
                <span class="badge">{{ $badgeLabel }}</span>
                <span class="status-text">PO #{{ $po->po_number ?? 'N/A' }}</span>
            </div>

            @if($notes)
            <div class="notes-box {{ $isRevised ? 'revise' : '' }}">
                <p><strong>{{ $isRevised ? 'Catatan revisi' : 'Keterangan' }}:</strong><br>{{ $notes }}</p>
            </div>
            @endif

            <div class="section-title">Detail</div>
            <table class="detail-table">
                <tr>
                    <td class="label">No. Purchase Order</td>
                    <td class="value"><span class="highlight">{{ $po->po_number ?? 'N/A' }}</span></td>
                </tr>
                <tr>
                    <td class="label">Supplier</td>
                    <td class="value">{{ $po->supplier->nama ?? 'N/A' }}</td>
                </tr>
                @if($sj)
                <tr>
                    <td class="label">No. Surat Jalan</td>
                    <td class="value">{{ $sj->no_surat_jalan }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal SJ</td>
                    <td class="value">{{ $sj->date ? \Carbon\Carbon::parse($sj->date)->translatedFormat('d F Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">ETD / ETA</td>
                    <td class="value">
                        {{ $sj->etd ? \Carbon\Carbon::parse($sj->etd)->translatedFormat('d M Y') : '-' }}
                        /
                        {{ $sj->eta ? \Carbon\Carbon::parse($sj->eta)->translatedFormat('d M Y') : '-' }}
                    </td>
                </tr>
                @endif
                @if($inv)
                <tr>
                    <td class="label">Status Tagihan</td>
                    <td class="value">{{ strtoupper(str_replace('_', ' ', $inv->status ?? 'N/A')) }}</td>
                </tr>
                @endif
                @if($po->createdBy)
                <tr>
                    <td class="label">PIC Purchasing</td>
                    <td class="value">{{ $po->createdBy->name }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Waktu Notifikasi</td>
                    <td class="value">{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
                </tr>
            </table>

            @if(($isPo && !in_array($type, [PurchaseOrderNotificationMail::TYPE_REJECTED, PurchaseOrderNotificationMail::TYPE_SUPPLIER_REJECTED], true) && $po->pdf_path) || ($isInvoice && $type === 'uploaded'))
            <div class="info-box">
                <p>Dokumen terkait dilampirkan pada email ini untuk referensi Anda.</p>
            </div>
            @endif

            <div class="cta-wrapper">
                <a href="{{ $portalUrl }}" class="cta-btn">Buka Supplier Portal</a>
            </div>

            <hr class="divider">
            <p style="font-size:13px;color:#718096;text-align:center;line-height:1.7;">
                Email otomatis dari sistem. <strong>Harap tidak membalas email ini.</strong>
            </p>
        </div>

        <div class="footer">
            <p class="company">Supplier Portal — Sistem Manajemen Pengadaan</p>
            <p>{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $shippingDocument->no_surat_jalan }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 10mm;
        }

        .header {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
            min-height: 90px;
        }

        .header-content {
            display: table;
            width: 100%;
            position: relative;
        }

        .header-left {
            display: table-cell;
            width: 33%;
            vertical-align: top;
        }

        .header-center {
            display: table-cell;
            width: 34%;
            vertical-align: top;
            text-align: center;
        }

        .header-right {
            display: table-cell;
            width: 33%;
            vertical-align: top;
            text-align: right;
        }

        .company-name {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-address {
            font-size: 9pt;
            line-height: 1.4;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 10pt;
            line-height: 1.6;
        }

        .info-section {
            margin: 15px 0;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
            font-size: 10pt;
        }

        .info-label {
            display: table-cell;
            width: 120px;
            font-weight: bold;
        }

        .info-value {
            display: table-cell;
            padding-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10pt;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            height: 30px;
        }

        table td.center {
            text-align: center;
        }

        table td.right {
            text-align: right;
        }

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            display: table;
            width: 100%;
            font-size: 10pt;
        }

        .footer-cell {
            display: table-cell;
            width: 33%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 30px;
            height: 50px;
        }

        .notes {
            margin-top: 20px;
            font-size: 9pt;
            line-height: 1.4;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .notes-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .container {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            table {
                page-break-inside: avoid;
            }

            img {
                max-width: 100%;
                height: auto;
            }
        }

        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }

        .no-print button {
            padding: 8px 15px;
            margin-left: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12pt;
        }

        .no-print button:hover {
            background-color: #0056b3;
        }

        .status-draft {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 10px;
            margin-bottom: 10px;
            color: #856404;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- No Print Actions -->
        <div class="no-print">
            <button onclick="window.print()">Cetak</button>
            <button onclick="window.close()">Tutup</button>
        </div>

        @if($shippingDocument->status === 'draft')
        <div class="status-draft">
            ⚠️ STATUS: DRAFT (Belum Dikonfirmasi)
        </div>
        @endif

        @if($shippingDocument->isApproved())
        <!-- Approval Watermark -->
        <div style="position: fixed; top: 50%; right: 30px; text-align: right; opacity: 0.2; pointer-events: none; z-index: 1000; transform: rotate(-45deg);">
            <div style="font-size: 80px; font-weight: bold; color: #28a745; letter-spacing: 3px;">APPROVED</div>
            <div style="font-size: 12px; margin-top: 10px;">Approved by: {{ $shippingDocument->approvedBy->name ?? '-' }}</div>
            <div style="font-size: 12px;">{{ $shippingDocument->approved_at?->format('d.m.Y H:i') }}</div>
        </div>
        @endif

        <!-- Header with QR Codes -->
        <div class="header">
            <div class="header-content" style="position: relative;">
                <!-- QR Code Top Left - Surat Jalan Number -->
                <div class="header-left" style="position: relative;">
                    <div style="position: absolute; top: 0; left: 0; width: 80px; height: 80px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode($shippingDocument->no_surat_jalan) }}"
                            alt="QR: {{ $shippingDocument->no_surat_jalan }}"
                            style="width: 100%; height: 100%; border: 1px solid #000;">
                    </div>
                    <div style="margin-left: 90px;">
                        <div class="company-name">{{ config('app.company_name', 'PERUSAHAAN') }}</div>
                        <div class="company-address">
                            {{ config('app.company_address', 'Alamat Perusahaan') }}<br>
                            Phone: {{ config('app.company_phone', '-') }}<br>
                            Email: {{ config('app.company_email', '-') }}
                        </div>
                    </div>
                </div>

                <!-- Center Title -->
                <div class="header-center">
                    <div class="title">SURAT JALAN</div>
                    <div class="subtitle">SHIPPING NOTICE</div>
                </div>

                <!-- QR Code Top Right - Website URL -->
                <div class="header-right" style="position: relative;">
                    <div style="position: absolute; top: 0; right: 0; width: 80px; height: 80px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode(config('app.url', 'https://supplierportal.com') . '/shipping-notice') }}"
                            alt="QR: Website URL"
                            style="width: 100%; height: 100%; border: 1px solid #000;">
                    </div>
                    <div style="margin-right: 90px;">
                        <div class="info-row">
                            <span class="info-label">No Surat Jalan :</span>
                            <span class="info-value" style="font-weight: bold;">{{ $shippingDocument->no_surat_jalan }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal :</span>
                            <span class="info-value" style="font-weight: bold;">{{ $shippingDocument->date->format('d.m.Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Halaman :</span>
                            <span class="info-value">1 of 1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier & Destination Info -->
        <div class="info-section">
            <div style="display: table; width: 100%;">
                <div style="display: table-cell; width: 50%;">
                    <div class="info-row">
                        <span class="info-label" style="width: 100px;">Supplier :</span>
                        <span class="info-value" style="font-weight: bold;">{{ $shippingDocument->purchaseOrder->supplier->nama ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label" style="width: 100px;">Supplier Code :</span>
                        <span class="info-value">{{ $shippingDocument->purchaseOrder->supplier->supplier_code ?? '-' }}</span>
                    </div>
                </div>
                <div style="display: table-cell; width: 50%;">
                    <div class="info-row">
                        <span class="info-label" style="width: 100px;">ETD :</span>
                        <span class="info-value">{{ $shippingDocument->etd ? $shippingDocument->etd->format('d.m.Y') : '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label" style="width: 100px;">ETA :</span>
                        <span class="info-value">{{ $shippingDocument->eta ? $shippingDocument->eta->format('d.m.Y') : '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="info-row" style="margin-top: 8px;">
                <span class="info-label" style="width: 100px;">Destination :</span>
                <span class="info-value">{{ $shippingDocument->purchaseOrder->supplier->alamat ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Items Table with QR Codes -->
        <table>
            <thead>
                <tr>
                    <th style="width: 4%; text-align: center;">NO</th>
                    <th style="width: 12%; text-align: center;">PO NUMBER</th>
                    <th style="width: 8%; text-align: center;">PO ITEM</th>
                    <th style="width: 10%; text-align: center;">MATERIAL CODE</th>
                    <th style="width: 35%;">DESCRIPTION</th>
                    <th style="width: 8%; text-align: center;">QTY</th>
                    <th style="width: 10%; text-align: center;">PO DATE</th>
                    <th style="width: 13%; text-align: center;">QR CODE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shippingDocument->items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center" style="font-size: 9pt;">{{ $purchaseOrder->po_number }}</td>
                    <td class="center" style="font-size: 9pt;">{{ $item->purchaseOrderItem->item_number ?? $index + 1 }}</td>
                    <td class="center" style="font-size: 9pt; font-weight: bold;">{{ $item->purchaseOrderItem->material_code ?? 'N/A' }}</td>
                    <td style="font-size: 9pt;">{{ $item->purchaseOrderItem->description }}</td>
                    <td class="center">{{ $item->quantity_shipped }}</td>
                    <td class="center" style="font-size: 9pt;">{{ $purchaseOrder->date->format('d.m.Y') }}</td>
                    <td class="center" style="padding: 2px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode($item->purchaseOrderItem->material_code ?? 'N/A') }}"
                            alt="QR: {{ $item->purchaseOrderItem->material_code }}"
                            style="width: 60px; height: 60px; border: 1px solid #999;">
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="right">TOTAL SHIPPED :</td>
                    <td class="center" style="font-weight: bold;">{{ $shippingDocument->items->sum('quantity_shipped') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <!-- Notes -->
        @if($shippingDocument->notes)
        <div class="notes">
            <div class="notes-label">Catatan :</div>
            <div>{{ $shippingDocument->notes }}</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-cell">
                <div>Pengiriman</div>
                <div style="margin-top: 35px;">........................</div>
            </div>
            <div class="footer-cell">
                <div>Diterima Oleh</div>
                <div style="margin-top: 35px;">........................</div>
            </div>
            <div class="footer-cell">
                <div>Mengetahui</div>
                <div style="margin-top: 35px;">........................</div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Uncomment to auto-print
            // window.print();
        }
    </script>
</body>

</html>
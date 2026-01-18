<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Notice - {{ $purchaseOrder->no_surat_jalan }}</title>
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
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header-left {
            display: table-cell;
            width: 15%;
            vertical-align: top;
            font-size: 9pt;
        }

        .header-left-content {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .header-left-row {
            display: flex;
            gap: 5px;
        }

        .header-left-label {
            font-weight: bold;
        }

        .header-center {
            display: table-cell;
            width: 45%;
            vertical-align: top;
            text-align: left;
            padding-left: 20px;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .supplier-logo {
            width: 120px;
            height: 80px;
            border: 2px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 80px;
            font-size: 10pt;
            margin-bottom: 5px;
        }

        .qr-box {
            width: 80px;
            height: 80px;
            display: inline-block;
        }

        .qr-box svg {
            width: 100%;
            height: 100%;
        }

        .title {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header-info {
            font-size: 10pt;
            line-height: 1.8;
        }

        .info-section {
            display: none;
        }

        .info-row {
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .info-label {
            display: inline-block;
            width: 140px;
            font-weight: normal;
        }

        .info-value {
            display: inline-block;
            font-weight: bold;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        table td.center {
            text-align: center;
        }

        table td.right {
            text-align: right;
        }

        .qr-cell {
            text-align: center;
        }

        .qr-small {
            width: 40px;
            height: 40px;
            display: inline-block;
        }

        .qr-small svg {
            width: 100%;
            height: 100%;
        }

        .qr-header-small {
            width: 60px;
            height: 60px;
            display: inline-block;
            margin-top: 5px;
        }

        .qr-header-small svg {
            width: 100%;
            height: 100%;
        }

        .page-info {
            font-size: 10pt;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="header-left-content">
                    <div class="header-left-row">
                        <div class="qr-box">
                            {!! QrCode::size(80)->generate($purchaseOrder->no_surat_jalan ?? '-') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-center">
                <div class="title">Shipping Notice</div>
                <div class="header-info">
                    <div><span class="header-left-label">No Surat Jalan</span> : {{ $purchaseOrder->no_surat_jalan ?? '-' }}</div>
                    <div><span class="header-left-label">Supplier Name</span> : {{ $purchaseOrder->supplier->nama ?? '-' }}</div>
                    <div><span class="header-left-label">Supplier Code</span> : {{ $purchaseOrder->supplier->kode ?? '-' }}</div>
                    <div><span class="header-left-label">ETD</span> : {{ $purchaseOrder->etd ? \Carbon\Carbon::parse($purchaseOrder->etd)->format('d.m.Y') : '-' }}</div>
                    <div><span class="header-left-label">ETA</span> : {{ $purchaseOrder->eta ? \Carbon\Carbon::parse($purchaseOrder->eta)->format('d.m.Y') : '-' }}</div>
                    <div><span class="header-left-label">Destination</span> : </div>
                </div>
            </div>
            <div class="header-right">
                <div class="page-info">
                    <div><strong>Date</strong> : {{ $purchaseOrder->etd ? \Carbon\Carbon::parse($purchaseOrder->etd)->format('d.m.Y') : '-' }}</div>
                    <div><strong>Page</strong> : 1 of 1</div>
                </div>
                <div style="font-size: 8pt; margin-top: 5px;">
                    URL Shipping Notice :<br>
                    <div class="qr-header-small">
                        {!! QrCode::size(60)->generate(route('purchase-orders.print-surat-jalan', $purchaseOrder->id)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Section -->
        <div class="info-section">
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">PO Number</th>
                    <th width="8%">PO Item</th>
                    <th width="12%">Material</th>
                    <th width="30%">Description</th>
                    <th width="8%">Qty</th>
                    <th width="12%">PO Date</th>
                    <th width="8%">QR Code</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrder->items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $purchaseOrder->po_number }}</td>
                    <td class="center">{{ $item->item_number ?? '-' }}</td>
                    <td>{{ $item->material_code ?? '-' }}</td>
                    <td>{{ $item->description ?? '-' }}</td>
                    <td class="center">{{ $item->quantity ?? 0 }}</td>
                    <td class="center">{{ $purchaseOrder->date ? \Carbon\Carbon::parse($purchaseOrder->date)->format('d.m.Y') : '-' }}</td>
                    <td class="qr-cell">
                        <div class="qr-small">
                            {!! QrCode::size(40)->generate($item->material_code ?? '-') !!}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data items</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

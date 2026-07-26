<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Supplier;
use Carbon\Carbon;

class PdfExtractorService
{
    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Extract data from PDF file
     *
     * @param string $pdfPath Path to PDF file
     * @return array Extracted data
     */
    public function extractPurchaseOrderData($pdfPath): array
    {
        try {
            // Get full path
            $fullPath = Storage::disk('public')->path($pdfPath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("PDF file not found: {$pdfPath}");
            }

            // Parse PDF
            $pdf = $this->parser->parseFile($fullPath);
            $text = $pdf->getText();
            
            // Extract data using regex patterns
            $companyName = $this->extractCompany($text);
            $data = [
                'po_number' => $this->extractPoNumber($text),
                'date' => $this->extractDate($text),
                'item_count' => $this->extractItemCount($text),
                'delivery_date' => $this->extractDeliveryDate($text),
                'currency' => $this->extractCurrency($text),
                'company_address' => $this->extractCompanyAddress($text),
                'items' => $this->extractItems($text),
                'supplier_id' => $this->extractSupplierId($companyName), // Auto-detect supplier
            ];

            return $data;
        } catch (\Exception $e) {
            \Log::error('PDF Extraction Error: ' . $e->getMessage());
            throw new \Exception('Gagal extract data dari PDF: ' . $e->getMessage());
        }
    }

    /**
     * Extract PO Number
     * Format: "1580057931 / 290925 / PS" or similar
     */
    protected function extractPoNumber($text): ?string
    {
        // Pattern untuk PO Number / Date
        $patterns = [
            '/PO\s*Number\s*\/\s*Date[:\s]*([0-9\s\/A-Z\-]+)/i',
            '/PO\s*Number[:\s]*([0-9\s\/A-Z\-]+)/i',
            '/PO\s*\/\s*Date[:\s]*([0-9\s\/A-Z\-]+)/i',
            '/([0-9]{10}\s*\/\s*[0-9]{6}\s*\/\s*[A-Z]+)/',
            '/([0-9]+\s*\/\s*[0-9]+\s*\/\s*[A-Z]+)/',
            // Try to find PO number pattern without label
            '/(\d{8,12}\s*\/\s*\d{6}\s*\/\s*[A-Z]{2,4})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $poNumber = trim($matches[1]);
                // Clean up extra spaces
                $poNumber = preg_replace('/\s+/', ' ', $poNumber);
                // Remove any trailing spaces or special chars
                $poNumber = trim($poNumber);
                if (!empty($poNumber) && strlen($poNumber) > 5) {
                    return $poNumber;
                }
            }
        }

        // Try alternative pattern: just numbers and slashes
        if (preg_match('/(\d+\s*\/\s*\d+\s*\/\s*[A-Z]+)/i', $text, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Extract Date
     * Format: "29.08.2025" or "29/08/2025" or "29-08-2025"
     */
    protected function extractDate($text): ?string
    {
        $patterns = [
            '/Date[:\s]*(\d{1,2}[\.\/\-]\d{1,2}[\.\/\-]\d{2,4})/i',
            '/(\d{1,2}[\.\/\-]\d{1,2}[\.\/\-]\d{4})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $dateStr = $matches[1];
                try {
                    // Try to parse different date formats
                    $date = $this->parseDate($dateStr);
                    return $date ? $date->format('Y-m-d') : null;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return null;
    }

    /**
     * Extract Item Count
     */
    protected function extractItemCount($text): int
    {
        $patterns = [
            '/Item[:\s]*(\d+)/i',
            '/Total\s*Item[:\s]*(\d+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return (int) $matches[1];
            }
        }

        // Count items from table rows
        $items = $this->extractItems($text);
        return count($items);
    }

    /**
     * Extract Contact Person
     */
    protected function extractContactPerson($text): ?string
    {
        $patterns = [
            '/Contact\s*Person[:\s]*([A-Za-z\s]+?)(?:\s*\/|\s*Telephone|$)/i',
            '/Contact\s*Person[:\s]*([A-Za-z\s]{3,50})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $contact = trim($matches[1]);
                // Remove telephone if included
                $contact = preg_replace('/\s*\d+.*$/', '', $contact);
                return $contact ?: null;
            }
        }

        return null;
    }

    /**
     * Extract Telephone
     */
    protected function extractTelephone($text): ?string
    {
        $patterns = [
            '/Telephone[:\s]*([\d\s\+\-\(\)]+)/i',
            '/Phone[:\s]*([\d\s\+\-\(\)]+)/i',
            '/Contact\s*Person[:\s]*[A-Za-z\s]+\s*\/\s*([\d\s\+\-\(\)]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return preg_replace('/\s+/', ' ', trim($matches[1]));
            }
        }

        return null;
    }

    /**
     * Extract Delivery Date
     */
    protected function extractDeliveryDate($text): ?string
    {
        $patterns = [
            '/Delivery\s*Date[:\s]*(\d{1,2}[\.\/\-]\d{1,2}[\.\/\-]\d{2,4})/i',
            '/Please\s*Deliver[^.]*?(\d{1,2}[\.\/\-]\d{1,2}[\.\/\-]\d{4})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                try {
                    $date = $this->parseDate($matches[1]);
                    return $date ? $date->format('Y-m-d') : null;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return null;
    }

    /**
     * Extract Currency
     */
    protected function extractCurrency($text): string
    {
        $patterns = [
            '/Currency[:\s]*([A-Z]{3})/i',
            '/\b(IDR|USD|EUR|SGD|MYR)\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return strtoupper($matches[1]);
            }
        }

        return 'IDR'; // Default
    }

    /**
     * Extract Company
     */
    protected function extractCompany($text): ?string
    {
        $patterns = [
            '/Company[:\s]*([A-Za-z0-9\s&\.]+?)(?:\n|Your\s*Company|Company\s*Number)/i',
            '/Company[:\s]*([A-Za-z0-9\s&\.]{5,100})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $company = trim($matches[1]);
                // Clean up - remove extra whitespace
                $company = preg_replace('/\s+/', ' ', $company);
                return $company ?: null;
            }
        }

        return null;
    }

    /**
     * Extract delivery / company address (Please Deliver To block on PO PDF).
     */
    protected function extractCompanyAddress($text): ?string
    {
        if (preg_match(
            '/Please\s+Deliver\s+To\s*:?\s*\r?\n(.+?)(?:\r?\n\s*(?:PO\s+Number|Purchase\s+Order))/is',
            $text,
            $matches
        )) {
            $lines = preg_split('/\r?\n/', trim($matches[1]));
            $lines = array_values(array_filter(array_map(
                static fn (string $line): string => rtrim(trim($line), ','),
                $lines
            )));
            if ($lines !== []) {
                return implode(', ', $lines);
            }
        }

        $fallback = trim((string) config('app.company_address', ''));
        return $fallback !== '' ? $fallback : null;
    }

    /**
     * Extract Supplier ID by matching company name with supplier
     */
    protected function extractSupplierId(?string $companyName): ?int
    {
        if (empty($companyName)) {
            return null;
        }

        // Clean company name
        $companyName = trim($companyName);
        $companyName = preg_replace('/\s+/', ' ', $companyName);
        
        // Try exact match first
        $supplier = Supplier::where('nama', 'LIKE', $companyName)->first();
        if ($supplier) {
            return $supplier->id;
        }

        // Try partial match (company name contains supplier name)
        $suppliers = Supplier::all();
        foreach ($suppliers as $supplier) {
            // Check if supplier name is in company name (case insensitive)
            if (stripos($companyName, $supplier->nama) !== false) {
                return $supplier->id;
            }
            // Check if company name is in supplier name (case insensitive)
            if (stripos($supplier->nama, $companyName) !== false) {
                return $supplier->id;
            }
        }

        // Try fuzzy match - remove common words like "PT", "CV", "Company", etc.
        $cleanCompanyName = preg_replace('/\b(PT|CV|UD|Company|Corp|Corporation|Ltd|Limited|Co)\b\.?/i', '', $companyName);
        $cleanCompanyName = trim($cleanCompanyName);
        
        foreach ($suppliers as $supplier) {
            $cleanSupplierName = preg_replace('/\b(PT|CV|UD|Company|Corp|Corporation|Ltd|Limited|Co)\b\.?/i', '', $supplier->nama);
            $cleanSupplierName = trim($cleanSupplierName);
            
            if (stripos($cleanCompanyName, $cleanSupplierName) !== false || 
                stripos($cleanSupplierName, $cleanCompanyName) !== false) {
                return $supplier->id;
            }
        }

        Log::warning("Supplier not found for company: {$companyName}");
        return null;
    }

    /**
     * Extract Items from table.
     * Supports: single-line tab rows, multi-line SAP rows (00010), and Word-style stacked rows.
     */
    protected function extractItems($text): array
    {
        $lines = array_map('trim', preg_split('/\r?\n/', $text));
        $headerLineNum = $this->findItemsTableHeaderLine($lines);

        if ($headerLineNum < 0) {
            Log::warning('Table header not found in PDF');

            return [];
        }

        $start = $headerLineNum + 1;
        while ($start < count($lines) && $this->isItemsHeaderContinuation($lines[$start])) {
            $start++;
        }

        $items = [];
        $currentItem = null;

        for ($i = $start; $i < count($lines); $i++) {
            $line = $lines[$i];
            if ($line === '') {
                continue;
            }

            if (preg_match('/^(?:Total|Grand\s*Total|Distributor|Footer|Summary)\b/i', $line)) {
                break;
            }

            // Satu baris penuh (tab/spasi): 00010 ASSV014 Deskripsi 5 100,000 500,000
            if (preg_match('/^(\d{1,5})[\s\t]+([A-Z0-9\-]+)[\s\t]+(.+?)[\s\t]+(\d+)[\s\t]+([\d\.,]+)[\s\t]+([\d\.,]+)\s*$/iu', $line, $m)) {
                if ($currentItem !== null) {
                    $items[] = $this->finalizeItem($currentItem);
                    $currentItem = null;
                }
                $items[] = $this->buildItemRow($m[1], $m[2], trim($m[3]), (int) $m[4], $m[5], $m[6]);
                continue;
            }

            // Qty + harga + net dalam satu baris: "15 185.000 2.775.000"
            if ($currentItem !== null && preg_match('/^(\d+)\s+([\d\.,]+)\s+([\d\.,]+)\s*$/', $line, $m)) {
                $currentItem['quantity'] = (int) $m[1];
                $currentItem['price_per_unit'] = $this->parseNumber($m[2]);
                $currentItem['net_value'] = $this->parseNumber($m[3]);
                $items[] = $this->finalizeItem($currentItem);
                $currentItem = null;
                continue;
            }

            // Harga + net (qty sudah di baris sebelumnya): "95.000 1.900.000"
            if ($currentItem !== null
                && (int) ($currentItem['quantity'] ?? 0) > 0
                && preg_match('/^([\d\.,]+)\s+([\d\.,]+)\s*$/', $line, $m)
            ) {
                $currentItem['price_per_unit'] = $this->parseNumber($m[1]);
                $currentItem['net_value'] = $this->parseNumber($m[2]);
                $items[] = $this->finalizeItem($currentItem);
                $currentItem = null;
                continue;
            }

            // Qty sendiri: "20"
            if ($currentItem !== null && preg_match('/^(\d{1,6})$/', $line, $m)) {
                $currentItem['quantity'] = (int) $m[1];
                continue;
            }

            // Awal baris item: "00010 FLT-OIL-001" atau "1 FLT-OIL-001 Deskripsi..."
            if (preg_match('/^(\d{1,5})\s+([A-Z0-9\-]+)(?:\s+(.+))?$/iu', $line, $m)) {
                if ($currentItem !== null) {
                    $items[] = $this->finalizeItem($currentItem);
                }
                $description = isset($m[3]) ? trim($m[3]) : '';
                $quantity = 0;
                $pricePerUnit = 0.0;
                $netValue = 0.0;

                if ($description !== '' && preg_match('/^(.+?)\s+(\d+)\s+([\d\.,]+)\s+([\d\.,]+)\s*$/u', $description, $tail)) {
                    $description = trim($tail[1]);
                    $quantity = (int) $tail[2];
                    $pricePerUnit = $this->parseNumber($tail[3]);
                    $netValue = $this->parseNumber($tail[4]);
                }

                $currentItem = $this->buildItemRow($m[1], $m[2], $description, $quantity, (string) $pricePerUnit, (string) $netValue);

                if ($quantity > 0 && $pricePerUnit > 0) {
                    $items[] = $this->finalizeItem($currentItem);
                    $currentItem = null;
                }

                continue;
            }

            // Lanjutan deskripsi
            if ($currentItem !== null) {
                $currentItem['description'] = trim(($currentItem['description'] ?? '').' '.$line);
            }
        }

        if ($currentItem !== null) {
            $items[] = $this->finalizeItem($currentItem);
        }

        return $items;
    }

    protected function findItemsTableHeaderLine(array $lines): int
    {
        foreach ($lines as $lineNum => $line) {
            if ($line === '') {
                continue;
            }
            if (preg_match('/Items?\s+Material/i', $line) && preg_match('/Description|Qty/i', $line)) {
                return $lineNum;
            }
            if (preg_match('/^Item\s+Material\s+Description/i', $line)) {
                return $lineNum;
            }
        }

        foreach ($lines as $lineNum => $line) {
            if ($line !== '' && preg_match('/Items?\s+Material/i', $line)) {
                return $lineNum;
            }
        }

        return -1;
    }

    protected function isItemsHeaderContinuation(string $line): bool
    {
        return (bool) preg_match('/^(?:Net\s*Value|Price\s*Per\s*Unit|Qty(?:\s+Price)?)\b/i', $line);
    }

    protected function buildItemRow(
        string $itemNumber,
        string $materialCode,
        string $description,
        int $quantity,
        string|float $pricePerUnit,
        string|float $netValue
    ): array {
        $price = is_numeric($pricePerUnit) ? (float) $pricePerUnit : $this->parseNumber((string) $pricePerUnit);
        $net = is_numeric($netValue) ? (float) $netValue : $this->parseNumber((string) $netValue);

        return [
            'item_number' => $itemNumber,
            'material_code' => $materialCode,
            'vendor_material' => null,
            'description' => $description,
            'quantity' => $quantity,
            'price_per_unit' => $price,
            'net_value' => $net > 0 ? $net : ($quantity > 0 && $price > 0 ? $price * $quantity : 0),
        ];
    }

    protected function finalizeItem(array $item): array
    {
        $qty = (int) ($item['quantity'] ?? 0);
        $price = (float) ($item['price_per_unit'] ?? 0);
        $net = (float) ($item['net_value'] ?? 0);

        if ($net <= 0 && $qty > 0 && $price > 0) {
            $item['net_value'] = $price * $qty;
        }

        $item['description'] = trim(preg_replace('/\s+/', ' ', (string) ($item['description'] ?? '')));

        return $item;
    }

    /**
     * Parse date string to Carbon instance
     */
    protected function parseDate($dateStr): ?Carbon
    {
        if (empty($dateStr)) {
            return null;
        }
        
        $dateStr = trim($dateStr);
        
        // Try different formats
        $formats = [
            'd.m.Y',
            'd/m/Y',
            'd-m-Y',
            'd.m.y',
            'd/m/y',
            'd-m-y',
            'Y-m-d',
            'Y.m.d',
            'Y/m/d',
        ];
        
        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateStr);
                if ($date && $date->year > 1900 && $date->year < 2100) {
                    return $date;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Try Carbon's parse as fallback
        try {
            $date = Carbon::parse($dateStr);
            if ($date && $date->year > 1900 && $date->year < 2100) {
                return $date;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to parse date: ' . $dateStr);
        }
        
        return null;
    }

    /**
     * Parse number string (remove commas, etc.)
     */
    protected function parseNumber($str): float
    {
        $str = trim((string) $str);
        if ($str === '') {
            return 0.0;
        }

        // Indonesian thousands with dot: 95.000, 1.900.000
        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $str)) {
            return (float) str_replace('.', '', $str);
        }

        // Thousands with comma: 100,000 / 8,500,000
        if (preg_match('/^\d{1,3}(,\d{3})+$/', $str)) {
            return (float) str_replace(',', '', $str);
        }

        // Decimal comma: 12,50
        if (preg_match('/,\d{1,2}$/', $str)) {
            $normalized = str_replace('.', '', $str);
            $normalized = str_replace(',', '.', $normalized);

            return (float) $normalized;
        }

        $str = preg_replace('/[^0-9\.]/', '', $str);

        return (float) $str;
    }
}


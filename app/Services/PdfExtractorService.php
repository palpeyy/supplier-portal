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
     * Extract Company Address
     */
    protected function extractCompanyAddress($text): ?string
    {
        // Try to extract address after company name
        $patterns = [
            '/Company[:\s]*[A-Za-z0-9\s&\.]+\n([A-Za-z0-9\s,\.\-]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
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
     * Extract Items from table
     */
    protected function extractItems($text): array
    {
        $items = [];
        
        // Split text into lines
        $lines = explode("\n", $text);
        
        // Look for table headers (Items, Material, Description, Qty, etc.)
        $inTable = false;
        $headersFound = false;
        $headerLineNum = -1;
        
        // First pass: find table header
        foreach ($lines as $lineNum => $line) {
            $line = trim($line);
            if (preg_match('/(Items|Item\s*#)|Material|Description|Qty|Quantity|Price|Value/i', $line)) {
                $headersFound = true;
                $headerLineNum = $lineNum;
                break;
            }
        }
        
        if (!$headersFound) {
            Log::warning('Table header not found in PDF');
            return $items;
        }
        
        // Second pass: extract items starting after header
        for ($i = $headerLineNum + 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            
            if (empty($line)) {
                continue;
            }
            
            // Stop if we find footer or total
            if (preg_match('/Distributor|Footer|Total|Grand\s*Total|Summary/i', $line)) {
                break;
            }
            
            // Pattern 1: Item number at start (00010, 00020, etc.)
            // Format: "00010 ASSV10-140 AFTER SALES SHIRT SHORT 14 SIZE SVC 5"
            if (preg_match('/^(\d{5})\s+(?:([A-Z0-9\-]+)\s+)?(.+?)\s+(\d+)(?:\s+([\d,\.]+))?(?:\s+([\d,\.]+))?$/i', $line, $matches)) {
                $itemNumber = $matches[1];
                $materialCode = isset($matches[2]) && !empty(trim($matches[2])) ? trim($matches[2]) : null;
                $description = trim($matches[3]);
                $quantity = (int) $matches[4];
                $pricePerUnit = isset($matches[5]) ? $this->parseNumber($matches[5]) : 0;
                $netValue = isset($matches[6]) ? $this->parseNumber($matches[6]) : ($pricePerUnit * $quantity);
                
                // Clean description - remove trailing numbers that might be quantity
                $description = preg_replace('/\s+\d+$/', '', $description);
                
                $items[] = [
                    'item_number' => $itemNumber,
                    'material_code' => $materialCode,
                    'vendor_material' => null,
                    'description' => $description,
                    'quantity' => $quantity,
                    'price_per_unit' => $pricePerUnit,
                    'net_value' => $netValue,
                ];
            }
            // Pattern 2: Tab-separated or multiple spaces
            elseif (preg_match('/^(\d{5})/', $line)) {
                $parts = preg_split('/\s{3,}|\t/', $line);
                if (count($parts) >= 3) {
                    $itemNumber = trim($parts[0]);
                    $materialCode = isset($parts[1]) && !empty(trim($parts[1])) ? trim($parts[1]) : null;
                    
                    // Description might be in multiple parts
                    $descParts = array_slice($parts, 2, -2); // Skip first 2 and last 2 (qty, price)
                    $description = implode(' ', $descParts);
                    
                    $quantity = isset($parts[count($parts) - 2]) ? (int) preg_replace('/[^0-9]/', '', $parts[count($parts) - 2]) : 0;
                    $pricePerUnit = isset($parts[count($parts) - 1]) ? $this->parseNumber($parts[count($parts) - 1]) : 0;
                    $netValue = 0; // Will be calculated later if needed
                    
                    if ($quantity > 0) {
                        $items[] = [
                            'item_number' => $itemNumber,
                            'material_code' => $materialCode,
                            'vendor_material' => null,
                            'description' => trim($description),
                            'quantity' => $quantity,
                            'price_per_unit' => $pricePerUnit,
                            'net_value' => $pricePerUnit * $quantity,
                        ];
                    }
                }
            }
        }
        
        return $items;
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
        // Remove commas and other non-numeric except decimal point
        $str = preg_replace('/[^0-9\.]/', '', $str);
        return (float) $str;
    }
}


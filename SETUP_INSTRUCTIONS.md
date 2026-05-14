# Instruksi Setup Fitur Multiple Shipping Documents

## 1. Jalankan Migrations

Untuk membuat tabel baru dan update struktur database, jalankan command:

```bash
php artisan migrate
```

Ini akan:
- Membuat tabel `shipping_documents`
- Membuat tabel `shipping_document_items`
- Menambah kolom `quantity_shipped` ke tabel `purchase_order_items`

## 2. Verifikasi Database

Cek struktur tabel di database:

```sql
-- Lihat tabel baru
DESC shipping_documents;
DESC shipping_document_items;

-- Lihat perubahan pada purchase_order_items
DESC purchase_order_items;
-- Seharusnya ada kolom `quantity_shipped`
```

## 3. Testing Fitur

### Test di Browser:

1. **Login sebagai Admin atau Supplier**
2. **Ke Menu: Penerimaan Barang**
3. **Klik "Surat Jalan" pada salah satu PO (status on_progress)**
4. **Akan muncul halaman baru untuk manage pengiriman:**
   - Status pengiriman barang per item
   - Tombol "Tambah Surat Jalan"
   - List existing surat jalan (jika ada)

5. **Buat Surat Jalan Pertama:**
   - Klik "Tambah Surat Jalan"
   - Isi form:
     - No Surat Jalan: "SJ-2026-001"
     - Tanggal: (pilih tanggal)
     - Barang yang Dikirim: tentukan qty per item
   - Klik "Simpan Surat Jalan"

6. **Verifikasi:**
   - Surat jalan muncul di daftar
   - Status pengiriman terupdate dengan progress bar
   - Quantity shipped terupdate

7. **Print Surat Jalan:**
   - Klik tombol "Cetak"
   - Akan membuka halaman print dengan format A4 siap cetak

## 4. Verifikasi Model & Relationship

Di MySQL verify:

```php
// Test di tinker
php artisan tinker

// Test model relationships
$po = PurchaseOrder::find(1);
$po->shippingDocuments; // Array of ShippingDocument
$po->isFullyShipped(); // true/false
$po->items->each->quantity_remaining; // Lihat qty sisa per item

$item = PurchaseOrderItem::find(1);
$item->quantity_shipped; // Qty yg sudah dikirim
$item->quantity_remaining; // Qty sisa

// Test create shipping document
$doc = ShippingDocument::create([
    'purchase_order_id' => 1,
    'no_surat_jalan' => 'SJ-TEST-001',
    'date' => now(),
    'status' => 'draft'
]);

ShippingDocumentItem::create([
    'shipping_document_id' => $doc->id,
    'purchase_order_item_id' => 1,
    'quantity_shipped' => 10
]);
```

## 5. Cek Routes

Verify routes sudah registered:

```bash
php artisan route:list | grep shipping
```

Seharusnya ada routes:
- GET  `/purchase-orders/{id}/shipping-documents`
- POST `/purchase-orders/{id}/shipping-documents`
- GET  `/purchase-orders/{id}/shipping-documents/{docId}/print`
- DELETE `/purchase-orders/{id}/shipping-documents/{docId}`

## 6. CSS/JS Dependencies

View sudah menggunakan Tailwind CSS dan Bootstrap yang sudah ada di project.
Tidak perlu install dependency tambahan.

## 7. Permissions Check

Verify access control masih bekerja:
- Admin: bisa akses semua
- Supplier: hanya bisa lihat PO milik mereka
- Dept. Head: bisa akses semua untuk monitoring

## 8. Backup Database (Recommended)

Sebelum deploy ke production:

```bash
# Backup database MySQL
mysqldump -u root -p supplier_portal > backup_before_update.sql
```

## Troubleshooting

### Error: SQLSTATE[42S02]: Table or view not found

→ Migrations belum dijalankan. Run: `php artisan migrate`

### Error: Class ShippingDocument not found  

→ Composer belum di-update. Run: `composer dump-autoload`

### Form tidak submit

→ Check CSRF token ada di meta `<meta name="csrf-token">`

### Auto-status update tidak works

→ Check relationship `isFullyShipped()` di model. Verify data yang masuk correct.

## Rollback (Jika diperlukan)

Untuk undo semua changes:

```bash
php artisan migrate:rollback --step=3
```

Ini akan:
- Drop tabel `shipping_documents`
- Drop tabel `shipping_document_items`
- Hapus kolom `quantity_shipped` dari `purchase_order_items`

---

## Summary Perubahan

- 3 migration files baru
- 2 model baru (ShippingDocument, ShippingDocumentItem)
- 2 view baru (shipping-documents, print-shipping-document)
- 4 controller methods baru
- Total update 4 files existing (models, controller, routes, 1 view)
- 4 routes baru

Semua changes backward-compatible dengan existing code.

# 📦 Multiple Shipping Notice dengan Partial Delivery & Fulfillment Tracking

## Ringkasan Implementasi

Sistem telah diupdate untuk mendukung **1 PO dapat memiliki multiple Shipping Notice (Surat Jalan)** dengan **partial delivery** dan **fulfillment tracking otomatis berdasarkan akumulasi semua shipping documents**.

---

### 1. Alur Pengiriman Barang

```
PO Approved (Status: on_progress)
    ↓
    ├─→ Buat Surat Jalan #1 (kirim beberapa barang)
    │   └─→ Update status item (quantity_shipped)
    │
    ├─→ Buat Surat Jalan #2 (kirim barang sisanya)
    │   └─→ Update status item lagi
    │
    └─→ Ketika SEMUA barang dikirim
        └─→ PO Status otomatis berubah menjadi "Received"
```

### 2. Fitur Utama

#### A. Manajemen Surat Jalan

**Lokasi:** Menu → Penerimaan Barang → Klik "Surat Jalan" pada PO

- **Lihat Status Pengiriman Real-time**
  - Progress bar untuk setiap item
  - Jumlah dikirim vs total PO
  - Sisa quantity yang belum dikirim

- **Buat Surat Jalan Baru**
  - Input nomor surat jalan
  - Pilih tanggal pengiriman
  - Tentukan quantity untuk setiap item
  - Sistem validasi: tidak boleh melebihi sisa qty

- **Cetak Surat Jalan**
  - Format siap cetak A4
  - Include: PO info, daftar barang, qty dikirim
  - Signature fields untuk pengiriman/penerima

- **Hapus Surat Jalan (Draft only)**
  - Hanya surat jalan dengan status "draft" yang bisa dihapus
  - Otomatis revert quantity_shipped

#### B. Tracking Barang Per Item

Setiap item pada PO sekarang memiliki field baru:
- `quantity_shipped`: Total yang sudah dikirim
- `quantity_remaining`: Auto-calculated = quantity - quantity_shipped

Contoh:
```
Item: Motor Socket A7.5
Total PO: 100 pcs
Surat Jalan #1: Kirim 30 pcs → quantity_shipped menjadi 30
Surat Jalan #2: Kirim 40 pcs → quantity_shipped menjadi 70
Surat Jalan #3: Kirim 30 pcs → quantity_shipped menjadi 100
Status: ✅ FULLY SHIPPED
```

#### C. Auto-Status Update

Ketika semua barang dalam PO sudah dikirim:
- PO status otomatis berubah dari `on_progress` → `received`
- Tidak perlu konfirmasi admin yang memakan waktu
- Keterangan otomatis diupdate menjadi "semua barang telah dikirim"

## Database Schema

### Tabel Baru

1. **shipping_documents**
   - id
   - purchase_order_id (foreign key)
   - no_surat_jalan (unik per PO)
   - date (tanggal pengiriman)
   - status (draft/confirmed/received)
   - notes (catatan tambahan)
   - timestamps

2. **shipping_document_items**
   - id
   - shipping_document_id (foreign key)
   - purchase_order_item_id (foreign key)
   - quantity_shipped
   - timestamps

### Tabel Diupdate

- **purchase_order_items** - tambah field `quantity_shipped`

## Navigasi & Panduan Penggunaan

### Untuk Admin

1. **Lihat Status Pengiriman:**
   - Buka "Penerimaan Barang" → Tab "Sedang Diproses"
   - Klik "Surat Jalan" di semua PO

2. **Monitor Pengiriman:**
   - Lihat progress bar untuk setiap item
   - Ketahui sisa barang yang belum dikirim
   - Dokumentasi otomatis setiap pengiriman

3. **Konfirmasi Penerimaan:**
   - Ketika semua barang dikirim, PO otomatis berubah status
   - Admin bisa lihat di tab "Selesai Diterima"

### Untuk Supplier

1. **Buat Surat Jalan:**
   - Klik tombol "Tambah Surat Jalan"
   - Input nomor surat jalan
   - Pilih tanggal pengiriman
   - Input quantity per item yang dikirim
   - Sistem akan validasi agar tidak melebihi sisa qty

2. **Cetak Surat Jalan:**
   - Klik tombol "Cetak" pada dokumen
   - Format siap untuk dicetak dan ditandatangani

3. **Hapus Draft:**
   - Jika masih draft, bisa dihapus tanpa dampak
   - Jika sudah confirmed, tidak bisa dihapus

### Untuk Dept. Head

- Bisa lihat dan monitor semua transaksi pengiriman
- Bisa print/cetak surat jalan untuk verifikasi

## Contoh Skenario Penggunaan

### Skenario 1: Pengiriman Bertahap

**PO-2026-001** total 100 unit item A

```
Hari 1:
- Buat Surat Jalan SJ-001 → kirim 30 unit
- Qty shipped: 30/100 (30%)

Hari 3:
- Buat Surat Jalan SJ-002 → kirim 40 unit
- Qty shipped: 70/100 (70%)

Hari 5:
- Buat Surat Jalan SJ-003 → kirim 30 unit
- Qty shipped: 100/100 (100%)
- ✅ PO Status auto-updated to "Received"
```

### Skenario 2: Multi-Item dengan Pengiriman Parsial

**PO-2026-002** contains:
- Item A: 50 pcs
- Item B: 100 pcs

```
Minggu 1 - Surat Jalan SJ-003:
- Item A: 50 pcs ✅ (fully shipped)
- Item B: 50 pcs (50/100 remaining)

Minggu 2 - Surat Jalan SJ-004:
- Item B: 50 pcs ✅ (fully shipped)
- ✅ PO Status → Received
```

## API Endpoints

```
GET    /purchase-orders/{id}/shipping-documents
       → Show shipping documents management page

POST   /purchase-orders/{id}/shipping-documents
       → Create new shipping document

GET    /purchase-orders/{id}/shipping-documents/{docId}/print
       → Print shipping document

DELETE /purchase-orders/{id}/shipping-documents/{docId}
       → Delete draft shipping document
```

## Validasi Sistem

1. **Duplicate Nomor Surat Jalan:** Tidak boleh ada 2 surat jalan dengan nomor sama pada PO yang sama
2. **Quantity Shipped:** Tidak boleh melebihi remaining quantity
3. **Hapus Dokumen:** Hanya draft yang bisa dihapus, akan auto-revert quantity_shipped
4. **Auto Status Update:** Otomatis bertransisi ke "received" saat semua barang dikirim

## Next Steps / Future Enhancement

Potential fitur tambahan untuk versi mendatang:
1. ✅ Verifikasi penerimaan per surat jalan (confirm/reject)
2. ✅ Attachment file untuk surat jalan (foto, bukti pengiriman)
3. ✅ Email notification saat surat jalan dibuat
4. ✅ Mobile app untuk verify pengiriman
5. ✅ Barcode scanning untuk tracking barang

## Troubleshooting

### Q: Surat jalan tidak bisa dihapus?
A: Hanya surat jalan dengan status "draft" yang bisa dihapus. Jika sudah "confirmed", hubungi admin.

### Q: Kenapa PO belum auto-update ke "received"?
A: Pastikan semua item sudah fully shipped (100%). Sistem otomatis check dan update.

### Q: Berapa banyak surat jalan yang bisa dibuat per PO?
A: Unlimited. Bisa dibuat sebanyak yang diperlukan sampai semua barang terkirim.

## Support

Untuk pertanyaan atau masalah, hubungi:
- IT Support atau contact admin

# 🛒 Aplikasi Kasir POS (Point of Sale) - TOKO WD

Aplikasi **Point of Sale (POS)** berbasis web menggunakan **Laravel 13** dan **PHP 8.3**. Sistem ini dirancang untuk mengelola transaksi kasir secara real-time, integrasi pembayaran QRIS, manajemen stok otomatis, serta kontrol hak akses berbasis peran (*Role-Based Access Control*).

---

## 📌 Ruang Lingkup & Fitur Utama (Modul B1, B2, B3)

### 1. Modul Penjualan & Kasir (B1)
* **Pencarian Produk Cepat**: Filter produk berdasarkan nama atau kode SKU secara real-time.
* **Keranjang & Kalkulasi Otomatis**: Perhitungan otomatis subtotal, diskon, total akhir, dan kembalian.
* **Integrasi Pembayaran QRIS**: Menampilkan poster QRIS resmi **TOKO WD** (`NMID: ID1025417538424`) secara otomatis saat kasir memilih metode pembayaran QRIS.
* **Manajemen Stok Real-Time**: Stok barang otomatis terpotong saat transaksi disimpan.

### 2. Keamanan & Hak Akses Pengguna (B2)
* **Role-Based Access Control (RBAC)**:
  * 👤 **Kasir**: Mengakses halaman transaksi kasir, melihat riwayat penjualan, dan mencetak struk.
  * 👨‍💼 **Admin**: Memiliki seluruh akses Kasir + kelola data master (Produk, Kategori, Pelanggan, Supplier, User), Laporan Keuangan & Export Excel, Log Aktivitas, serta **Pembatalan Transaksi (Void)**.
* **Proteksi Pembatalan Transaksi (Void)**:
  * Fitur pembatalan transaksi dikunci khusus untuk role **Admin**.
  * Jika **Kasir** mencoba mengakses fitur pembatalan, sistem memblokirnya dengan error `403 Forbidden`.
  * Pembatalan transaksi oleh Admin otomatis mengubah status menjadi `CANCELLED` dan mengembalikan stok barang (*restock*).

### 3. Perancangan & Dokumentasi Sistem (B3)
* **Entity Relationship Diagram (ERD)**: Perancangan relasi tabel *Users, Customers, Categories, Products, Suppliers, Purchases, PurchaseItems, Sales, SaleItems, ActivityLogs,* dan *Settings*.
* **Use Case Diagram**: Pemetaan interaksi dan batasan hak akses aktor Kasir dan Admin.
* **Flowchart Sistem**: Alur logika proses transaksi kasir dan proses pembatalan transaksi oleh Admin.

---

## 🛠️ Spesifikasi Teknologi

* **Framework**: Laravel 13
* **Bahasa Pemrograman**: PHP 8.3
* **Database**: SQLite / MySQL
* **Frontend**: HTML5, CSS3, JavaScript (ES6), Bootstrap 5, Bootstrap Icons

---

## 🚀 Cara Menjalankan Project di Lokal

1. **Clone Repository**
   ```bash
   git clone [https://github.com/daris429/pos-app.git](https://github.com/daris429/pos-app.git)
   cd pos-app
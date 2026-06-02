# 🍜 Sistem POS Kasir & Manajemen Stok

## **Bakso Cahaya Mandiri**

---

## 📌 Deskripsi

Aplikasi ini merupakan sistem **Point of Sale (POS)** berbasis web yang digunakan untuk membantu operasional **Bakso Cahaya Mandiri**, mulai dari transaksi kasir, manajemen stok bahan baku, hingga laporan penjualan.

Sistem ini dirancang untuk mempermudah:

* Proses transaksi di kasir
* Pengelolaan stok bahan baku
* Monitoring penjualan
* Pengelolaan pesanan ke dapur

---

## 🚀 Fitur Utama

### 🛒 POS (Kasir)

* Input pesanan dengan cepat
* Keranjang belanja otomatis
* Checkout & pembayaran
* Cetak struk

### 📦 Manajemen Stok

* Tambah/edit bahan baku
* Sistem satuan (gram, pcs, ml)
* Pengurangan stok otomatis saat transaksi

### 🍲 Recipe / Resep

* Setiap menu memiliki resep bahan
* Digunakan untuk auto deduct stok

### 📊 Laporan

* Laporan penjualan
* Export data
* Monitoring transaksi

### 👨‍🍳 Role Dapur

* Melihat pesanan masuk
* Update status pesanan

### 👥 Role User

* Admin → kelola semua data
* Kasir → transaksi
* Owner → laporan
* Dapur → lihat pesanan

---

## 🧑‍💻 Teknologi yang Digunakan

* **Laravel** (Backend)
* **Blade Template** (Frontend)
* **Bootstrap 5** (UI)
* **MySQL** (Database)
* **JavaScript** (Interaksi POS)

---

## ⚙️ Cara Install & Menjalankan

### 1. Clone Repository

```bash
git clone https://github.com/SintaMustika/projectsystemGobakso.git
```

### 2. Masuk ke Folder

```bash
cd projectsystemGobakso
```

### 3. Install Dependency

```bash
composer install
```

### 4. Copy File Environment

```bash
cp .env.example .env
```

### 5. Generate Key

```bash
php artisan key:generate
```

### 6. Setup Database

* Buat database di phpMyAdmin
* Edit `.env`:

```
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Migrasi Database

```bash
php artisan migrate
```

### 8. Jalankan Server

```bash
php artisan serve
```

---

## 🔐 Akun Default

| Role  | Email                                     | Password |
| ----- | ----------------------------------------- | -------- |
| Admin | [admin@gmail.com](mailto:admin@gmail.com) | password |
| Kasir | [kasir@gmail.com](mailto:kasir@gmail.com) | password |
| Dapur | [dapur@gmail.com](mailto:dapur@gmail.com) | password |

---

## 🖥️ Tampilan Sistem

* Dashboard Admin
* POS Kasir Modern
* Manajemen Menu & Stok
* Halaman Dapur
* Laporan Penjualan

---

## 🎯 Tujuan Sistem

* Mempermudah operasional usaha bakso
* Mengurangi kesalahan pencatatan manual
* Monitoring stok secara real-time
* Meningkatkan efisiensi kerja

---

## 👨‍🎓 Dibuat Oleh

**Sinta Mustika**
Project Sistem Informasi POS Kasir

---

## ⭐ Penutup

Aplikasi ini diharapkan dapat membantu usaha kecil menengah dalam mengelola transaksi dan stok secara digital dan efisien.

---

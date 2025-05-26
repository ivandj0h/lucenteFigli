# 🛒 Lucente Figli - E-Commerce PHP Proyek SMK

Lucente Figli adalah proyek e-commerce sederhana berbasis PHP procedural, dirancang untuk kebutuhan tugas sekolah (SMK). Aplikasi ini mendukung autentikasi pengguna, dashboard admin, sistem keranjang belanja, checkout, dan histori pesanan.

## 📁 Fitur Utama

### 👤 User

- Register dan login
- Lihat produk
- Tambah ke keranjang
- Checkout
- Lihat riwayat pemesanan

### 🛠️ Admin

- Dashboard admin dengan sidebar
- CRUD Produk
- CRUD Kategori
- CRUD User

### 🧾 Order & Checkout

- User dapat melakukan checkout dari cart
- Riwayat pesanan dengan status dinamis:
  - `Pending` → `Dikirim` → `Selesai` _(otomatis dengan JS)_

## 💾 Teknologi Digunakan

- PHP 8.2+
- MySQL 8
- Bootstrap 5
- Docker (untuk MySQL & phpMyAdmin)

## 🚀 Cara Menjalankan

### 1. Clone Repository

```bash
git clone <repo-url>
cd lucenteFigli
```

### 2. Jalankan MySQL dengan Docker

```bash
docker run --name mysql-php8 -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=lucente_db -p 3306:3306 -d mysql:8.0
```

### 3. (Opsional) Jalankan phpMyAdmin

```bash
docker run --name phpmyadmin-lucente --link mysql-php8:db -e PMA_HOST=db -p 8080:80 -d phpmyadmin/phpmyadmin
```

### 4. Jalankan PHP server lokal

```bash
php -S localhost:8000
```

### 5. Import database

Gunakan phpMyAdmin atau MySQL CLI untuk mengimpor `lucente_db.sql`.

## 📂 Struktur Folder

```
lucenteFigli/
├── admin/
│   ├── product/
│   ├── category/
│   └── user/
├── user/
│   ├── cart.php
│   ├── orders.php
│   └── index.php
├── includes/
├── config/
├── public/
└── assets/
```

## 📌 Catatan

- Folder `uploads/` di-ignore dari Git (gunakan `.gitkeep` bila perlu)
- Status pesanan hanya disimulasikan dengan JavaScript, bukan real-time

## 👨‍💻 Kontributor

> Dibangun untuk pembelajaran SMK

---

Selamat ngoding 🚀🔥

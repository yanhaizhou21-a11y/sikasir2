# Sikasir - Sistem Kasir Restoran/Cafe

Sistem kasir berbasis web untuk restoran atau cafe dengan fitur lengkap untuk mengelola produk, transaksi, meja, dan stok bahan baku. Dibangun dengan Laravel 12 dan menggunakan sistem role-based access control (RBAC) untuk mengelola akses pengguna.

## 📋 Daftar Isi

- [Tentang Project](#tentang-project)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Fitur](#fitur)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Cara Instalasi](#cara-instalasi)
- [Konfigurasi](#konfigurasi)
- [Default Credentials](#default-credentials)
- [Struktur Project](#struktur-project)
- [Penggunaan](#penggunaan)
- [License](#license)

## 🎯 Tentang Project

Sikasir adalah sistem point of sale (POS) yang dirancang khusus untuk restoran atau cafe. Sistem ini mendukung multi-role dengan dashboard terpisah untuk setiap role, manajemen produk dengan kategori dan subkategori, sistem transaksi, manajemen meja dengan QR code, serta tracking stok bahan baku untuk bar dan kitchen.

## 🛠 Teknologi yang Digunakan

### Backend
- **Laravel 12** - PHP Framework
- **PHP 8.2+** - Bahasa pemrograman
- **SQLite** (default) / **MySQL** - Database
- **Spatie Laravel Permission** - Role & Permission Management
- **Laravel Breeze** - Authentication scaffolding
- **SimpleSoftwareIO/simple-qrcode** - QR Code Generator

### Frontend
- **Tailwind CSS 3.1** - CSS Framework
- **Alpine.js 3.4** - JavaScript Framework (lightweight)
- **Vite 7** - Build tool & dev server
- **Axios** - HTTP Client

### Development Tools
- **Laravel Pint** - Code style fixer
- **PHPUnit** - Testing framework
- **Laravel Pail** - Log viewer

## ✨ Fitur

### 1. **Sistem Multi-Role**
   - **Admin**: Akses penuh ke semua fitur
   - **Kasir**: Melakukan transaksi penjualan
   - **Bar**: Mengelola stok bahan baku bar
   - **Kitchen**: Mengelola stok bahan baku kitchen
   - **Owner**: Dashboard khusus owner
   - **Pelanggan**: Role untuk customer

### 2. **Manajemen Produk**
   - CRUD produk lengkap
   - Kategori dan subkategori produk
   - Barcode untuk setiap produk
   - Upload gambar produk
   - Harga modal dan harga jual
   - QR Code untuk barcode produk
   - Relasi produk dengan bahan baku

### 3. **Sistem Transaksi**
   - Point of Sale (POS) untuk kasir
   - Multiple payment methods (Cash, QRIS)
   - Invoice otomatis
   - Riwayat transaksi
   - Detail transaksi dengan item

### 4. **Manajemen Meja**
   - CRUD meja
   - Generate QR Code otomatis untuk setiap meja
   - Link unik per meja untuk pemesanan

### 5. **Manajemen Bahan Baku (Ingredients)**
   - Tracking stok bahan baku
   - Pemisahan lokasi (Bar & Kitchen)
   - Minimum stock alert
   - Stock log untuk audit
   - Relasi dengan produk (Bill of Materials)

### 6. **Manajemen User**
   - CRUD user (Admin only)
   - Assign role ke user
   - Profile management

### 7. **Dashboard per Role**
   - Dashboard khusus untuk setiap role
   - Redirect otomatis berdasarkan role setelah login

### 8. **Authentication & Authorization**
   - Login/Register
   - Email verification
   - Password reset
   - Role-based middleware protection

## 📦 Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js >= 18.x dan NPM
- SQLite (default) atau MySQL/MariaDB
- Extension PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## 🚀 Cara Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/yanhaizhou21-a11y/sikasir2.git
cd sikasir
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

**Opsi A: Menggunakan SQLite (Default)**
```bash
# Pastikan file database.sqlite ada
touch database/database.sqlite
```

**Opsi B: Menggunakan MySQL**
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikasir
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migration & Seeder

```bash
# Jalankan migration
php artisan migrate

# Seed roles dan user default
php artisan db:seed --class=RoleSeeder

# (Opsional) Seed meja dengan QR code
php artisan db:seed --class=TableSeeder
```

### 6. Buat Storage Link

```bash
php artisan storage:link
```

### 7. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Jalankan Server

```bash
# Development server
php artisan serve

# Atau gunakan script composer untuk dev lengkap (server + queue + logs + vite)
composer dev
```

Akses aplikasi di: `http://localhost:8000`

## ⚙️ Konfigurasi

### File `.env`

Pastikan konfigurasi berikut sudah benar:

```env
APP_NAME=Sikasir
APP_ENV=local
APP_KEY=base64:... (generate dengan php artisan key:generate)
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
# atau untuk MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sikasir
# DB_USERNAME=root
# DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🔑 Default Credentials

Setelah menjalankan `RoleSeeder`, user default yang tersedia:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@gmail.com | admin123 |
| Kasir | kasir@gmail.com | kasir123 |
| Bar | bar@gmail.com | bar123 |
| Kitchen | kitchen@gmail.com | kitchen123 |

## 📁 Struktur Project

```
sikasir/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Controller untuk semua fitur
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form request validation
│   ├── Models/               # Eloquent models
│   ├── Providers/            # Service providers
│   └── helpers.php           # Helper functions
├── config/                   # Konfigurasi aplikasi
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories
├── public/                   # Public assets
│   ├── qrcodes/             # Generated QR codes
│   └── storage/             # Storage symlink
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # CSS files
│   └── js/                  # JavaScript files
├── routes/
│   ├── web.php              # Web routes
│   └── auth.php             # Authentication routes
├── storage/                  # Storage files
├── tests/                    # Test files
├── vendor/                   # Composer dependencies
├── composer.json            # PHP dependencies
├── package.json             # Node.js dependencies
├── vite.config.js           # Vite configuration
└── tailwind.config.js       # Tailwind configuration
```

## 📖 Penggunaan

### Login
1. Buka `http://localhost:8000/login`
2. Masukkan email dan password sesuai role
3. Setelah login, akan di-redirect ke dashboard sesuai role

### Admin Dashboard
- Kelola produk, kategori, subkategori
- Kelola user dan assign role
- Kelola bahan baku (ingredients)
- Lihat semua transaksi
- Kelola meja

### Kasir Dashboard
- Lakukan transaksi penjualan
- Pilih produk dan jumlah
- Proses pembayaran (Cash/QRIS)
- Generate invoice

### Bar/Kitchen Dashboard
- Lihat daftar bahan baku sesuai lokasi
- Update stok bahan baku
- Monitor minimum stock

### Manajemen Meja
- Buat meja baru (admin)
- QR code akan otomatis di-generate
- Scan QR code untuk akses menu per meja

## 🔧 Development

### Menjalankan Development Server

```bash
# Jalankan semua service sekaligus (server, queue, logs, vite)
composer dev
```

### Testing

```bash
php artisan test
```

### Code Style

```bash
php artisan pint
```

## 📝 Catatan

- Pastikan folder `public/qrcodes` memiliki permission write untuk generate QR code
- Pastikan folder `storage/app/public` memiliki permission write untuk upload gambar
- Untuk production, set `APP_DEBUG=false` di file `.env`
- Pastikan `APP_KEY` sudah di-generate

## 🤝 Contributing

Contributions are welcome! Silakan buat issue atau pull request.

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

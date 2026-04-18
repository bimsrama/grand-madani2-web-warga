# Grand Madani 2 – Portal Warga RT
## Setup & Installation Guide

---

## Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- MySQL 8.0+

---

## Step 1 — Scaffold the Base Laravel Project

Open a terminal **where PHP and Composer are available**, then:

```bash
cd C:\Users\bimazeelarizka\.gemini\antigravity\scratch

# Create the Laravel project
composer create-project laravel/laravel grand-madani-2-base

# Copy all custom files from grand-madani-2/ into grand-madani-2-base/
# (they will overwrite the default Laravel files)
```

**Files to copy from `grand-madani-2/` into your new project:**

| Directory | What's there |
|---|---|
| `database/migrations/2024_01_01_*.php` | All custom migrations |
| `database/seeders/DatabaseSeeder.php` | Sample data seeder |
| `app/Models/` | All models |
| `app/Http/Controllers/` | All controllers |
| `app/Http/Middleware/ResidentAuthenticated.php` | Resident auth middleware |
| `app/Services/` | WhatsApp + Letter services |
| `resources/views/` | All Blade templates |
| `routes/web.php` | All routes |
| `config/services.php` | Fonnte API config |
| `bootstrap/app.php` | Middleware alias registration |

---

## Step 2 — Install Composer Dependencies

```bash
composer require barryvdh/laravel-dompdf
```

---

## Step 3 — Configure Environment

Copy and fill in your `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="Grand Madani 2"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=grand_madani_2
DB_USERNAME=root
DB_PASSWORD=your_password

SESSION_DRIVER=database
SESSION_LIFETIME=120

# Fonnte WhatsApp API token (get from https://fonnte.com/dashboard)
FONNTE_TOKEN=your_fonnte_token_here
```

---

## Step 4 — Create Database & Run Migrations

```bash
# Create the MySQL database first
mysql -u root -p -e "CREATE DATABASE grand_madani_2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed with sample data
php artisan db:seed

# Create the storage symbolic link
php artisan storage:link
```

---

## Step 5 — Install Frontend Assets

The project uses **Tailwind CSS via CDN** + **Alpine.js via CDN** — no npm build needed.
Just run the dev server:

```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## Step 6 — Login Credentials

### Admin Login
URL: `http://localhost:8000/admin/login`

```
Email:    admin@grandmadani2.com
Password: GrandMadani2025!
```

### Resident Login
URL: `http://localhost:8000/warga/login`

Select a house from dropdown, enter **last 4 digits** of the WA number:

| Resident | WA Number | Password |
|---|---|---|
| Budi Santoso (Blok A/1)   | 6281234567001 | **7001** |
| Dewi Rahayu (Blok A/2)    | 6281234567002 | **7002** |
| Ahmad Fauzi (Blok A/3)    | 6281234567003 | **7003** |
| Sari Indah (Blok B/1)     | 6281234567004 | **7004** |
| Rizky Pratama (Blok B/2)  | 6281234567005 | **7005** |
| Fitri Handayani (Blok B/3)| 6281234567006 | **7006** |
| Hendra Kusuma (Blok C/1)  | 6281234567007 | **7007** |

---

## Feature Checklist

| Feature | Route | Access |
|---|---|---|
| Laporan Keuangan RT (table + modal + PDF) | `/keuangan` | Public |
| Pasar Warga (Preloved + Direktori Jasa)   | `/pasar-warga` | Public |
| Aduan & Forum (form + announcement board) | `/aduan-forum` | Public |
| Kartu IPL Warga (12-month grid)           | `/warga/kartu-ipl` | Resident (session) |
| Pantauan Keamanan (CCTV per-camera access)| `/warga/cctv` | Resident (session + DB grant) |
| Admin – Laporan Keuangan                  | `/admin/keuangan` | Admin (auth) |
| Admin – Manajemen IPL + WA Invoice        | `/admin/ipl` | Admin (auth) |
| Admin – Generator Surat RT                | `/admin/surat` | Admin (auth) |
| Admin – Kelola Akses CCTV                 | `/admin/cctv` | Admin (auth) |

---

## WhatsApp API (Fonnte)

1. Register at [fonnte.com](https://fonnte.com)
2. Connect your WhatsApp number
3. Copy your API token
4. Set `FONNTE_TOKEN=your_token` in `.env`

> When Admin marks a month as **Lunas** → system auto-generates PDF invoice → sends WA message + PDF to resident's phone.

---

## CCTV Stream Format

The `embed_url` for each camera can be:
- An HLS stream URL (played via `<iframe>` or `<video>` tag)
- An IP camera HTTP endpoint
- An embed-compatible stream service URL

Set the URL per camera through the **Admin CCTV panel** (`/admin/cctv`).

---

## Architecture Notes

| Concern | Decision |
|---|---|
| Resident auth | Server-side Laravel session (`session_id` in DB), **NO localStorage** |
| Admin auth | Laravel's built-in `Auth::` against `users` table |
| CCTV access | Per-camera M2M via `camera_resident` pivot + DB grant by Admin |
| PDF generation | `barryvdh/laravel-dompdf` with custom Blade templates |
| WA delivery | Fonnte API via `Http::attach()` |
| File storage | `storage/app/public/` with symlink to `public/storage/` |

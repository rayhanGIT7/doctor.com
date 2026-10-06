# MediBook – Online Doctor Appointment System

Patients search doctors and book a time slot. Admins manage hospitals, doctors, categories and schedules. Doctors manage their own appointments and schedule.

Built with plain PHP 8 (simple OOP / MVC), MySQL/MariaDB (PDO) and Bootstrap 5. No Composer or framework needed.

## Requirements

- PHP 8.1 or newer with `pdo_mysql` enabled
- MySQL 8+ or MariaDB 10.4+ (XAMPP works)

> **XAMPP note:** open `C:\xampp\php\php.ini`, find `;extension=pdo_mysql` and remove the `;`. Then restart Apache.

## Setup

1. Create the database and sample data:

   ```bash
   mysql -u root < database/schema.sql
   mysql -u root < database/seed.sql
   ```

   (Or import both files with phpMyAdmin, schema first.)

2. Check the database settings in `config/config.php`.

3. Run the app:

   **Option A – PHP built-in server (easiest)**

   ```bash
   php -S localhost:8000 -t public public/index.php
   ```

   Open http://localhost:8000

   **Option B – XAMPP Apache**

   Put the project folder in `C:\xampp\htdocs\` (for example `htdocs\doctor.com`) and open
   http://localhost/doctor.com/. The root `.htaccess` sends every request to `public/`.
   `mod_rewrite` must be enabled (it is enabled by default in XAMPP).

## Demo accounts

| Role    | Login (email or phone)                      | Password     |
|---------|---------------------------------------------|--------------|
| Admin   | `admin@medibook.test`                       | `admin123`   |
| Patient | `patient@medibook.test`                     | `patient123` |
| Doctor  | `ayesha@medibook.test` (and 7 other doctors) | `doctor123`  |

## Main URLs

| Area          | URL                 |
|---------------|---------------------|
| Website       | `/`, `/doctors`     |
| Patient       | `/my/dashboard`     |
| Admin panel   | `/admin`            |
| Doctor panel  | `/doctor`           |

## Going live

- Set `'debug' => false` in `config/config.php`
- Point the web server document root at `public/`
- Use HTTPS (the session cookie is marked `secure` automatically on HTTPS)
- Make `storage/logs` and `public/uploads` writable

See [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) for how the code is organised.

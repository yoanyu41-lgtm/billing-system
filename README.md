# CityTech - Installment & Billing Management System
ប្រព័ន្ធគ្រប់គ្រងការលក់ និងបង់រំលស់ទំនិញ (Laravel 12 + Tailwind CSS + Vite)

---

## 📋 តម្រូវការជាមុន (System Requirements)
មុនពេលដំឡើងនៅលើកុំព្យូទ័រថ្មី សូមប្រាកដថាកុំព្យូទ័រនោះមានកម្មវិធីទាំងនេះរួចរាល់៖
* **PHP**: `>= 8.2` (ជាមួយ extensions: `pdo_mysql`, `curl`, `mbstring`, `openssl`, `gd` ឬ `imagick`)
* **Composer**: កំណែចុងក្រោយ
* **Node.js**: `>= 18.x` & **NPM**
* **MySQL** ឬ **MariaDB** (អាចប្រើតាមរយៈ **Laragon** ឬ **XAMPP**)
* **Git**

---

## 🚀 របៀបដំឡើងនៅលើកុំព្យូទ័រផ្សេង (Installation Guide)

### ជំហានទី ១៖ ទាញយកកូដ (Clone Repository)
បើក Terminal (Command Prompt ឬ Git Bash) រួចវាយពាក្យបញ្ជា៖
```bash
git clone https://github.com/yoanyu41-lgtm/billing-system.git
cd billing-system
```

---

### ជំហានទី ២៖ រៀបចំ File កំណត់រចនាសម្ព័ន្ធ (.env)
ចម្លង file `.env.example` ទៅជា `.env`៖
* **លើ Windows (Command Prompt):**
  ```cmd
  copy .env.example .env
  ```
* **លើ Git Bash / Linux / macOS:**
  ```bash
  cp .env.example .env
  ```

---

### ជំហានទី ៣៖ ដំឡើង Packages (Composer & NPM)
ដំឡើងកញ្ចប់បណ្ណាល័យ PHP និង JavaScript៖
```bash
composer install
npm install
```

---

### ជំហានទី ៤៖ បង្កើត Encryption Key
បង្កើត App Key សម្រាប់ប្រព័ន្ធសុវត្ថិភាព Laravel៖
```bash
php artisan key:generate
```

---

### ជំហានទី ៥៖ បង្កើត Database និង Migrate ទិន្នន័យ
1. បើក **HeidiSQL**, **phpMyAdmin**, ឬ **MySQL Workbench** រួចបង្កើត Database ថ្មីមួយឈ្មោះ៖
   ```sql
   CREATE DATABASE installment_mangement_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. បើក file `.env` ដើម្បីផ្ទៀងផ្ទាត់ឈ្មោះ Database និង Password៖
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=installment_mangement_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. បញ្ចូលតារាង និងទិន្នន័យគំរូដើម (Migrations & Seeders):
   ```bash
   php artisan migrate --seed
   ```

---

### ជំហានទី ៦៖ ភ្ជាប់ Storage Link សម្រាប់រូបភាព
ដើម្បីឱ្យរូបភាពបង្កាន់ដៃបង់ប្រាក់ និងរូបទំនិញបង្ហាញត្រឹមត្រូវ៖
```bash
php artisan storage:link
```

---

### ជំហានទី ៧៖ ដំណើរការប្រព័ន្ធ (Run Application)

#### វិធីទី ១៖ ប្រើប្រាស់ Script ស្វ័យប្រវត្តិ (សម្រាប់ Windows)
ចុច Double-click លើ file **`dev.bat`** ក្នុង Folder គម្រោង ឬវាយក្នុង CMD៖
```cmd
dev.bat
```
*(Script នេះនឹងពិនិត្យ និងដំណើរការទាំង Laravel Server, Vite Dev Server និង Background Workers ដោយស្វ័យប្រវត្តិ)*

#### វិធីទី ២៖ ដំណើរការដោយដៃតាម Terminal
```bash
# Terminal 1: ដំណើរការ Laravel Backend
php artisan serve

# Terminal 2: ដំណើរការ Vite Frontend
npm run dev
```

បើកកម្មវិធីរុករក (Web Browser) ហើយចូលទៅកាន់៖
👉 **`http://127.0.0.1:8000`** ឬ **`http://localhost:8000`**

---

## 👥 គណនីសម្រាប់ចូលប្រើប្រាស់ដំបូង (Default Credentials)
បន្ទាប់ពីដំណើរការ `--seed` រួច អ្នកអាចចូលប្រើគណនី Admin ឬ Staff ដូចខាងក្រោម៖

| តួនាទី (Role) | អ៊ីមែល (Email) | ពាក្យសម្ងាត់ (Password) |
| :--- | :--- | :--- |
| **Admin** | `admin@example.com` | `password` |
| **Staff** | `staff@example.com` | `password` |

*(សម្គាល់៖ លោកអ្នកអាចផ្លាស់ប្តូរពាក្យសម្ងាត់ ឬបង្កើតគណនីថ្មីក្នុងផ្ទាំងគ្រប់គ្រងអ្នកប្រើប្រាស់)*

---

## ⚙️ ការកំណត់បន្ថែម (Optional Settings)
* **Telegram Bot**: កំណត់ `TELEGRAM_BOT_TOKEN` នៅក្នុង `.env` ឬក្នុងផ្ទាំង Admin Settings។
* **Ngrok (សម្រាប់ Webhook លើ Localhost)**:
  ```bash
  ngrok http 8000
  ```

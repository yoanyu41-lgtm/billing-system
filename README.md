# Billing & Installment Management System

ប្រព័ន្ធគ្រប់គ្រងការលក់ និងការបង់រំលស់ទំនិញ (Billing & Installment Management System) ត្រូវបានអភិវឌ្ឍឡើងដោយប្រើប្រាស់ Laravel Framework សហការជាមួយ TailwindCSS និង MySQL Database។

---

## លក្ខណៈពិសេសចម្បងៗ (Key Features)

- **គ្រប់គ្រងការលក់ និងវិក្កយបត្រ (Sales & Invoice Management):** ចេញវិក្កយបត្រលក់ដាច់ និងបង់រំលស់
- **គ្រប់គ្រងកាលវិភាគបង់រំលស់ (Installment Schedules):** បង្កើតតារាងបង់ប្រាក់រំលស់ប្រចាំខែ និងការប្រាក់ដោយស្វ័យប្រវត្តិ
- **តាមដានការទូទាត់យឺតយ៉ាវ (Late Payment & Penalty):** គណនាប្រាក់ពិន័យ និងតាមដានអតិថិជនជំពាក់
- **គ្រប់គ្រងព័ត៌មានអតិថិជន និងអ្នកធានា (Customer & Guarantor):** ពិនិត្យប្រវត្តិ និងវាយតម្លៃឥណទាន
- **គ្រប់គ្រងស្តុកទំនិញ (Stock & Inventory):** តាមដានចលនាស្តុកទំនិញ និងការទិញចូល
- **របាយការណ៍ និងទាញយកជា PDF (Reports & Invoices):** របាយការណ៍ចំណូល-ចំណាយ ប្រចាំថ្ងៃ/ខែ គាំទ្រពុម្ពអក្សរខ្មែរ
- **គ្រប់គ្រងសិទ្ធិបុគ្គលិក (Roles & Permissions):** បែងចែកតួនាទី Admin, Manager, Cashier ដោយសុវត្ថិភាព

---

## របៀបដំឡើង និងដំណើរការគម្រោង (Installation Guide)

1. ទាញយក Dependencies:
```bash
composer install
npm install
```

2. កំណត់រចនាសម្ព័ន្ធ Environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. បង្កើត Tables ក្នុង Database:
```bash
php artisan migrate --seed
```

4. Compile Front-end Assets:
```bash
npm run build
```

5. ដំណើរការ Server:
```bash
php artisan serve
```

---

## អ្នកបង្កើតគម្រោង (Author)
- **ឈ្មោះ:** Yoan Yu

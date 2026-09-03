# ជំពូកទី ២៖ ការរំលឹកទ្រឹស្ដី និងបច្ចេកវិទ្យាពាក់ព័ន្ធ (Literature Review & Technologies)

ឯកសារនេះត្រូវបានរៀបចំ និងវិភាគផ្អែកលើស្ថាបត្យកម្ម និងកូដជាក់ស្ដែងនៃ **ប្រព័ន្ធគ្រប់គ្រងការបង់រំលស់ និងវិក្កយបត្រ (Billing & Installment Management System)**។

---

## ២.១- រំលឹកទ្រឹស្ដីពាក់ព័ន្ធប្រធានបទ

### ២.១.១- ការណែនាំអំពីទ្រឹស្ដីទូទៅទាក់ទងនិងប្រធានបទ
* **ក. ប្រព័ន្ធព័ត៌មាន (Information System):**
  * ប្រព័ន្ធរួមបញ្ចូលគ្នារវាងមនុស្ស (Users/Staff), ដំណើរការការងារ (Processes), ទិន្នន័យ (Data) និងបច្ចេកវិទ្យា (Technology) ដើម្បីប្រមូល រក្សាទុក ដំណើរការ និងចែកចាយព័ត៌មានគាំទ្រដល់ការសម្រេចចិត្ត និងគ្រប់គ្រងអាជីវកម្ម។
* **ខ. ដំណើរការនៃប្រព័ន្ធគ្រប់គ្រងការបង់រំលស់ (Installment Workflow):**
  * លំហូរការងារជាប្រព័ន្ធ៖ ចាប់ផ្ដើមពីការបង្កើតការលក់ (Sale Order) -> ការបង្កើតកាលវិភាគបង់ប្រាក់រំលស់ (Amortization/Installment Schedule) -> ការកត់ត្រាការទូទាត់ប្រចាំខែ (Installment Payment) -> រហូតដល់ការទូទាត់ផ្ដាច់ (Pay-Off/Clearance)។
* **គ. ការគ្រប់គ្រងហានិភ័យ និងការផាកពិន័យ (Risk & Penalty Management):**
  * យន្តការតាមដានការយឺតយ៉ាវ (Overdue/Late Tracking), ការគណនាប្រាក់ពិន័យតាមលក្ខខណ្ឌកិច្ចសន្យា (Penalty Calculation), និងការគ្រប់គ្រងហានិភ័យឥណទានអតិថិជន។
* **ឃ. ការវាយតម្លៃឥណទាន និងអ្នកធានា (Credit Assessment & Guarantor):**
  * ដំណើរការផ្ទៀងផ្ទាត់ប្រវត្តិហិរញ្ញវត្ថុ ការដាក់ពិន្ទុឥណទាន (Credit Scoring) និងការកត់ត្រាព័ត៌មានអ្នកធានា (Guarantor) មុននឹងអនុម័តកិច្ចសន្យាបង់រំលស់។
* **ង. ការគ្រប់គ្រងស្តុក និងចលនាទំនិញ (Inventory & Stock Movement):**
  * ការគ្រប់គ្រងការទិញទំនិញចូល (Purchases), ការកាត់ស្តុកស្វ័យប្រវត្តិនៅពេលលក់ចេញ (Stock Deductions), និងការតាមដានចលនាស្តុក (Stock Movement Audit Logs)។
* **ច. វដ្ដជីវិតនៃការអភិវឌ្ឍន៍កម្មវិធី (SDLC - Software Development Life Cycle):**
  * ដំណាក់កាលអភិវឌ្ឍន៍ជាប្រព័ន្ធរួមមាន៖ ការសិក្សាតម្រូវការ (Requirement Analysis), ការរចនាប្លង់ប្រព័ន្ធ (System Design), ការសរសេរកូដ (Implementation), ការធ្វើតេស្ត (Testing), និងការដាក់ឱ្យដំណើរការ (Deployment)។
* **ឆ. បទពិសោធន៍ និងចំណុចប្រទាក់អ្នកប្រើប្រាស់ (UX/UI - User Experience / User Interface):**
  * ការរចនាផ្ទាំងកម្មវិធីឱ្យមានភាពទាក់ទាញ ងាយស្រួលប្រើប្រាស់ (Intuitive), ការបង្ហាញព័ត៌មានច្បាស់លាស់តាមរយៈ Dashboard, Cards, Modals, និង Responsive Tables។

---

### ២.១.២- ការណែនាំអំពីអ៊ីនធឺណិត និងបច្ចេកវិទ្យាគេហទំព័រ (Internet and Web Technology)
* **ក. World Wide Web (WWW):** ប្រព័ន្ធព័ត៌មានសកលដែលភ្ជាប់ឯកសារគេហទំព័រ (Hypertext) តាមរយៈបណ្តាញអ៊ីនធឺណិត។
* **ខ. Web Browser:** កម្មវិធីរុករកសម្រាប់បកប្រែភាសា HTML, CSS, JavaScript ឱ្យទៅជាផ្ទាំងរូបភាព និងអន្តរកម្មលើអេក្រង់ (Google Chrome, Edge, Safari...)។
* **គ. Web Server:** ម៉ាស៊ីនបម្រើការ (ដូចជា Apache/Nginx) សម្រាប់ទទួល Request និងឆ្លើយតបទំព័រគេហទំព័រទៅកាន់ Browser។
* **ឃ. Web Page:** ទំព័រឯកសារលើគេហទំព័រ (បង្កើតឡើងដោយ HTML/Blade Template) ដែលផ្ទុកនូវទិន្នន័យ អត្ថបទ និងរូបភាព។
* **ង. HTTP / HTTPS (Hypertext Transfer Protocol Secure):** ពិធីការទំនាក់ទំនងទិន្នន័យរវាង Client (Browser) និង Server ដោយមានការការពារសុវត្ថិភាពតាមរយៈ SSL/TLS Encryption។
* **ច. URL (Uniform Resource Locator):** អាសយដ្ឋានចង្អុលបង្ហាញទីតាំងជាក់លាក់នៃធនធាន (Resources) ឬ Route នៅលើគេហទំព័រ។
* **ឆ. Framework:** គ្រោងការណ៍ស្តង់ដារដែលផ្តល់នូវបណ្ណាល័យ (Libraries) និងគំរូរចនាសម្ព័ន្ធកូដ ដែលជួយឱ្យការអភិវឌ្ឍប្រព័ន្ធកាន់តែលឿន មានសុវត្ថិភាព និងមានរបៀបរៀបរយ។
* **ជ. Cookie:** ឯកសារទិន្នន័យទំហំតូចដែលត្រូវបានរក្សាទុកនៅលើ Web Browser (Client-side) សម្រាប់ចងចាំជម្រើសរបស់អ្នកប្រើប្រាស់, CSRF Token និង Session Identifier។
* **ឈ. Session:** យន្តការរក្សាទុកទិន្នន័យបណ្តោះអាសន្នរបស់អ្នកប្រើប្រាស់នៅលើ Web Server (Server-side) ក្នុងអំឡុងពេលដែល User កំពុងដំណើរការក្នុងប្រព័ន្ធ (User Authentication State & Flash Messages)។

---

### ២.១.៣- ការណែនាំអំពីឧបករណ៍សម្រាប់អភិវឌ្ឍន៍កម្មវិធី (Development Tools)
* **ក. Visual Studio Code (VS Code):** កម្មវិធីកែសម្រួលកូដ (Source-code Editor) សំបូរបែប គាំទ្រ Extensions ជាច្រើនសម្រាប់ PHP, Blade, Tailwind និង JavaScript។
* **ខ. Git:** ប្រព័ន្ធគ្រប់គ្រងកំណែកូដ (Distributed Version Control System) ជួយតាមដានការកែប្រែប្រវត្តិសាស្ត្រនៃ Source Code។
* **គ. GitHub:** សេវាកម្ម Cloud សម្រាប់ផ្ទុក Git Repository ជួយសម្រួលដល់ការគ្រប់គ្រង Codebase ការ Backup និងការធ្វើការងារជាក្រុម។
* **ឃ. XAMPP:** កញ្ចប់កម្មវិធី Local Server បញ្ចូលគ្នារួមមាន Apache, MySQL/MariaDB, និង PHP សម្រាប់ដំណើរការប្រព័ន្ធលើកុំព្យូទ័រក្នុងដំណាក់កាលអភិវឌ្ឍន៍។
* **ង. Postman:** ឧបករណ៍សម្រាប់ធ្វើតេស្ត ស្នើសុំ (Send Requests) និងត្រួតពិនិត្យដំណើរការរបស់ API Endpoints។
* **ច. Ngrok:** ឧបករណ៍បង្កើត Secure Tunnel ភ្ជាប់ពី Localhost ទៅកាន់ Public Internet យ៉ាងរហ័ស ដើម្បីងាយស្រួល Demo លើទូរស័ព្ទ ឬធ្វើតេស្ត Webhook។
* **ឆ. Composer:** កម្មវិធីគ្រប់គ្រងកញ្ចប់បណ្ណាល័យរបស់ភាសា PHP (Dependency Manager) សម្រាប់ទាញយក និង Update packages ដូចជា Laravel Framework, Spatie, DomPDF។
* **ជ. NPM (Node Package Manager):** កម្មវិធីគ្រប់គ្រងកញ្ចប់ JavaScript Dependencies សម្រាប់ដំណើរការ Build Toolchain (Vite, Tailwind, Alpine.js)។
* **ឈ. Telegram (Bot & Webhook):** ថ្នាលផ្ញើសារដែលប្រព័ន្ធប្រើប្រាស់ Bot API ដើម្បីផ្ញើសារជូនដំណឹងដោយស្វ័យប្រវត្តិនូវរាល់ប្រតិបត្តិការសំខាន់ៗ (Payment Alert, Late Payment Reminders)។

---

### ២.១.៤- ការណែនាំអំពីបច្ចេកវិទ្យាផ្នែកខាងមុខ (Front-End)
* **ក. HTML (HyperText Markup Language):** គ្រោងឆ្អឹងស្តង់ដារសម្រាប់រៀបចំទម្រង់មាតិកានៃគេហទំព័រ (ប្រើតាមរយៈ Laravel Blade Templates)។
* **ខ. CSS (Cascading Style Sheets):** ភាសារចនាទម្រង់ ពណ៌ ពុម្ពអក្សរ និង Layout របស់គេហទំព័រឱ្យមានសោភ័ណភាព។
* **គ. JavaScript (JS):** ភាសាសរសេរកូដសម្រាប់បន្ថែមអន្តរកម្មលើផ្ទាំង UI, គណនាតម្លៃលេខភ្លាមៗលើ Form, និងផ្ទៀងផ្ទាត់ទិន្នន័យ (Dynamic Interactivity)។
* **ឃ. Tailwind CSS:** Utility-first CSS Framework ទំនើប ដែលអនុញ្ញាតឱ្យរចនា UI ស្អាត និង Responsive លឿនដោយមិនបាច់សរសេរ CSS Custom ច្រើន។
* **ង. Vite:** Build Tool ជំនាន់ថ្មីដែលមានល្បឿនលឿនបំផុតសម្រាប់ Compile Assets (CSS/JS) និង Hot Module Replacement (HMR)។
* **ច. Alpine.js:** Lightweight JavaScript Framework សម្រាប់គ្រប់គ្រង UI State តូចៗលើ HTML ផ្ទាល់ ដូចជា Dropdown, Tabs, Modal Popups, និង Flash Alerts។
* **ឆ. Axios:** Promise-based HTTP Client សម្រាប់ JavaScript ក្នុងការ Call ទាញយក និងបញ្ជូនទិន្នន័យ API ទៅកាន់ Back-End ដោយមិនបាច់ Reload ទំព័រ (Asynchronous Request)។

---

### ២.១.៥- ការណែនាំអំពីបច្ចេកវិទ្យាផ្នែកខាងក្រោយ (Back-End)
* **ក. PHP (Hypertext Preprocessor):** ភាសាសរសេរកូដផ្នែក Server-side ដ៏មានប្រជាប្រិយភាព និងរឹងមាំ (ប្រព័ន្ធនេះប្រើ PHP 8.2+)។
* **ខ. Laravel Framework:** PHP Web Framework កម្រិតខ្ពស់ (ប្រើ Laravel 12) ដែលផ្ដល់នូវរចនាសម្ព័ន្ធ MVC, Routing, Middleware, Eloquent ORM, និងប្រព័ន្ធសុវត្ថិភាពខ្ពស់។
* **គ. ស្ថាបត្យកម្ម MVC (Model-View-Controller):**
  * **Model:** តំណាងឱ្យតារាងទិន្នន័យ និង Logic នៃ Database (Customer, Sale, Installment, Payment...)។
  * **View:** ផ្ទាំងបង្ហាញព័ត៌មានទៅកាន់អ្នកប្រើប្រាស់ (Blade Views)។
  * **Controller:** អ្នកសម្របសម្រួល ទទួល Request ពី User ទៅទាញទិន្នន័យពី Model រួចបញ្ជូនទៅបង្ហាញនៅ View។
* **ឃ. OOP (Object-Oriented Programming):** វិធីសាស្ត្រសរសេរកម្មវិធីដោយផ្អែកលើ Object & Class រួមមានគោលការណ៍ Encapsulation, Inheritance, Polymorphism និង Abstraction។
* **ង. DBMS & RDBMS (Relational Database Management System):** ប្រព័ន្ធគ្រប់គ្រងមូលដ្ឋានទិន្នន័យទំនាក់ទំនង ដែលធានានូវលក្ខណៈសម្បត្តិ ACID និងភាពត្រឹមត្រូវនៃទិន្នន័យហិរញ្ញវត្ថុ។
* **ច. MySQL:** ប្រព័ន្ធគ្រប់គ្រងមូលដ្ឋានទិន្នន័យ RDBMS ដែលប្រើប្រាស់ក្នុងប្រព័ន្ធនេះសម្រាប់ផ្ទុកទិន្នន័យតារាងទាំងអស់។
* **ឆ. Eloquent ORM:** បច្ចេកវិទ្យារបស់ Laravel សម្រាប់ធ្វើការជាមួយ Database ដោយផ្ទាល់តាមរយៈ Object PHP ជំនួសឱ្យការសរសេរ SQL Queries ដោយដៃ។
* **ជ. API (Application Programming Interface) & JSON:** ចំណុចប្រទាក់សម្រាប់ផ្លាស់ប្ដូរទិន្នន័យរវាងប្រព័ន្ធផ្សេងៗគ្នា ដោយប្រើទម្រង់ទិន្នន័យស្តង់ដារ JSON (JavaScript Object Notation)។
* **ឈ. Node.js:** បរិស្ថាន JavaScript Runtime លើម៉ាស៊ីនកុំព្យូទ័រ ដែលប្រើសម្រាប់ដំណើរការបណ្ណាល័យ Front-End និងដំណើរការ Vite Server។
* **ញ. Apache Web Server:** Web Server ដ៏ពេញនិយមសម្រាប់ដំណើរការ PHP Scripts និងបម្រើសេវា HTTP Requests។
* **ដ. RBAC (Role-Based Access Control - Spatie Laravel-Permission):** យន្តការគ្រប់គ្រង និងបែងចែកសិទ្ធិអំណាចអ្នកប្រើប្រាស់ (Super Admin, Manager, Cashier) តាមរយៈ Role និង Permission។
* **ឋ. PDF Engine (DomPDF / Snappy):** កញ្ចប់បណ្ណាល័យសម្រាប់បំប្លែងទំព័រ HTML/Blade ឱ្យទៅជាឯកសារវិក្កយបត្រ និងរបាយការណ៍បង់រំលស់ជាទម្រង់ PDF សម្រាប់ Print ឬ Download។
* **ឌ. Data Backup & Recovery:** ប្រព័ន្ធស្វ័យប្រវត្តិកម្មសម្រាប់ Backup មូលដ្ឋានទិន្នន័យ MySQL និងឯកសារសំខាន់ៗ ដើម្បីធានាសុវត្ថិភាព និងការសង្គ្រោះទិន្នន័យបន្ទាន់។

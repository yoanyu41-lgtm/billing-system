{{-- ============================================================
     CityTech Billing — Brand Design Tokens (Navy + Emerald)
     ប្រព័ន្ធពណ៌ និងការរចនា - CityTech Billing
     
     Single source of truth for colors, radius, shadows.
     ប្រភពតែមួយគត់សម្រាប់ពណ៌, radius, shadows
     
     Premium, flat, NO gradients. See DESIGN-SYSTEM.md.
     រចនាបែប Premium, ផ្ទះស្មើ, គ្មាន gradients
     
     Included in every layout <head>.
     បញ្ចូលក្នុងគ្រប់ layout <head>
     
     🔤 FONT SYSTEM - ប្រព័ន្ធពុម្ពអក្សរ:
        📝 English & Khmer: Khmer OS Siemreap
        អង់គ្លេស និង ខ្មែរ: Khmer OS Siemreap
     
     🎨 BRAND COLOR SYSTEM - ប្រព័ន្ធពណ៌ BRAND:
     ✅ ពណ៌ដែលគួរប្រើ (Recommended Colors):
        🟢 Green (#059669)  - បង់រួច, ជោគជ័យ, ផ្ទៀងផ្ទាត់ (Paid, Success, Confirmed)
        🔴 Red (#DC2626)    - លើសកំណត់, មិនទាន់បង់, កំហុស (Overdue, Unpaid, Error)
        🟡 Yellow (#D97706) - កំពុងរង់ចាំ, ជិតដល់កាលកំណត់, ការព្រមាន (Pending, Due Soon, Warning)
        🔵 Blue (#2563EB)   - ព័ត៌មាន, សកម្មភាព (Information, Actions)
        ⚫ Gray (#64748B)   - មិនសកម្ម, បិទប្រើប្រាស់ (Inactive, Disabled)
     
     ❌ ពណ៌ដែលអត់គួរប្រើ (Colors to AVOID):
        - Neon/Fluorescent colors (ពណ៌ភ្លឺខ្លាំង - ឈឺភ្នែក)
        - Pure black backgrounds (ផ្ទៃខ្មៅសុទ្ធ - លើកលែងតែ dark mode)
        - Purple (ពណ៌ស្វាយ - មិន professional សម្រាប់ payments)
        - Brown/muddy colors (ពណ៌កខ្វក់ - មិនស្អាត)
        - Random colors without system (ពណ៌ចៃដន្យគ្មានប្រព័ន្ធ)
============================================================ --}}
<style>
    /* ── Font Configuration - ការកំណត់ពុម្ពអក្សរ ── */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Battambang:wght@400;700;900&display=swap');
    
    /* Font family setup - ការដំឡើងពុម្ពអក្សរ */
    /* English: Poppins, Khmer: Battambang */
    * {
        font-family: 'Poppins', 'Battambang', 'Khmer OS Battambang', 'Khmer-System', sans-serif;
    }
    
    /* English text - អត្ថបទអង់គ្លេស */
    *:lang(en) {
        font-family: 'Poppins', sans-serif;
    }
    
    /* Khmer text - អត្ថបទខ្មែរ */
    *:lang(km) {
        font-family: 'Battambang', 'Khmer OS Battambang', 'Khmer-System', sans-serif;
        line-height: 1.8; /* Better spacing for Khmer - គម្លាតល្អសម្រាប់ខ្មែរ */
    }
    
    /* Fallback for mixed content - សម្រាប់អត្ថបទចម្រុះ */
    body {
        font-family: 'Poppins', 'Battambang', 'Khmer OS Battambang', 'Khmer-System', sans-serif;
    }

    :root {
        /* ── Brand - ពណ៌ Brand ── */
        --brand:            #1E3A5F;   /* deep professional navy (primary) - ខៀវចាស់ professional */
        --brand-hover:      #162D47;   /* navy hover / pressed - ពេលចុច */
        --brand-active:     #112138;   /* navy darkest - ខ្មៅបំផុត */
        --on-brand:         #FFFFFF;

        --secondary:        #2563EB;   /* support blue - ខៀវជំនួយ */
        --secondary-hover:  #1D4ED8;

        --accent:           #059669;   /* emerald — "paid / success" - បៃតង "បង់រួច / ជោគជ័យ" */
        --accent-hover:     #047857;
        --accent-soft:      #34D399;   /* emerald highlight on dark surfaces - បៃតងភ្លឺលើផ្ទៃងងឹត */

        /* ── Sidebar (dark navy surface) - របារចំហៀង (ផ្ទៃខៀវងងឹត) ── */
        --sidebar:          #0F1E33;
        --sidebar-raised:   #18293F;
        --sidebar-text:     #94A3B8;
        --sidebar-text-hi:  #E2E8F0;
        --sidebar-hover:    rgba(255,255,255,0.06);

        /* ── Semantic status - ស្ថានភាពអត្ថន័យ ── */
        --success:          #059669;   /* Green - បង់រួច/ជោគជ័យ */
        --warning:          #D97706;   /* Yellow - ការព្រមាន/កំពុងរង់ចាំ */
        --danger:           #DC2626;   /* Red - លើសកំណត់/កំហុស */
        --info:             #2563EB;   /* Blue - ព័ត៌មាន */

        /* ── Surfaces & text - ផ្ទៃ & អត្ថបទ ── */
        --bg:               #FFFFFF;   /* Background - ផ្ទៃខាងក្រោយពណ៌ស */
        --surface:          #FFFFFF;   /* Surface - ផ្ទៃ */
        --surface-muted:    #F8FAFC;   /* Muted surface - ផ្ទៃស្រាល */
        --text:             #0F172A;   /* Text - អត្ថបទ */
        --text-muted:       #64748B;   /* Muted text - អត្ថបទស្រាល */
        --text-subtle:      #94A3B8;   /* Subtle text - អត្ថបទស្រាលជាង */
        --border:           #E2E8F0;   /* Border - ស្រទាប់ */
        --border-strong:    #CBD5E1;   /* Strong border - ស្រទាប់ខ្លាំង */
        --ring:             #1E3A5F;

        /* ── Radius - កាត់ជ្រុង ── */
        --radius-sm: 10px;
        --radius:    14px;
        --radius-lg: 18px;
        --radius-pill: 999px;

        /* ── Premium layered shadows (soft, never harsh) - ស្រមោលទន់ ── */
        --shadow-sm: 0 1px 2px rgba(15,23,42,.06);
        --shadow:    0 1px 3px rgba(15,23,42,.08), 0 1px 2px rgba(15,23,42,.04);
        --shadow-md: 0 4px 14px rgba(15,23,42,.08);
        --shadow-lg: 0 12px 32px -8px rgba(15,23,42,.16);
        --shadow-brand: 0 8px 20px -6px rgba(30,58,95,.45);
        --shadow-accent: 0 8px 20px -6px rgba(5,150,105,.40);
    }
</style>

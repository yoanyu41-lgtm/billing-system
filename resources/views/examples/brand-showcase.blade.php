@extends('layouts.app')

@section('content')
<div class="content">
    {{-- ចំណងជើង (Header) --}}
    <div class="mb-6">
        <h1 lang="km" class="text-3xl font-bold mb-2">ការបង្ហាញ Brand Colors និង Fonts</h1>
        <p lang="en" class="text-gray-600">Brand Colors and Fonts Showcase</p>
    </div>

    {{-- ផ្នែកទី 1: Status Badges - ស្លាកស្ថានភាព --}}
    <div class="card mb-6">
        <h2 lang="km" class="text-xl font-bold mb-4">ស្លាកស្ថានភាព (Status Badges)</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h3 lang="km" class="text-sm font-semibold text-gray-700 mb-2">វិធីទី 1: Badge Classes</h3>
                <div class="flex flex-wrap gap-2">
                    <span class="badge-paid">បង់រួច (Paid)</span>
                    <span class="badge-overdue">លើសកំណត់ (Overdue)</span>
                    <span class="badge-pending">កំពុងរង់ចាំ (Pending)</span>
                    <span class="badge-info">ព័ត៌មាន (Info)</span>
                    <span class="badge-inactive">មិនសកម្ម (Inactive)</span>
                </div>
            </div>
            
            <div>
                <h3 lang="km" class="text-sm font-semibold text-gray-700 mb-2">វិធីទី 2: Pill Classes</h3>
                <div class="flex flex-wrap gap-2">
                    <span class="pill pill-paid">បង់រួច</span>
                    <span class="pill pill-overdue">លើសកំណត់</span>
                    <span class="pill pill-ongoing">កំពុងដំណើរការ</span>
                    <span class="pill pill-pending">កំពុងរង់ចាំ</span>
                </div>
            </div>
            
            <div>
                <h3 lang="km" class="text-sm font-semibold text-gray-700 mb-2">វិធីទី 3: Tailwind Classes</h3>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold">បង់រួច</span>
                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold">លើសកំណត់</span>
                    <span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-xs font-bold">រង់ចាំ</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ផ្នែកទី 2: Buttons - ប៊ូតុង --}}
    <div class="card mb-6">
        <h2 lang="km" class="text-xl font-bold mb-4">ប៊ូតុង (Buttons)</h2>
        
        <div class="flex flex-wrap gap-3">
            <button class="btn-brand-green">
                <i class="fas fa-check"></i> ផ្ទៀងផ្ទាត់ (Confirm)
            </button>
            
            <button class="btn-brand-blue">
                <i class="fas fa-eye"></i> មើលព័ត៌មាន (View Details)
            </button>
            
            <button class="btn-brand-yellow">
                <i class="fas fa-clock"></i> កំពុងរង់ចាំ (Pending)
            </button>
            
            <button class="btn-brand-red">
                <i class="fas fa-trash"></i> លុបចោល (Delete)
            </button>
        </div>
    </div>

    {{-- ផ្នែកទី 3: Alert Messages - សារជូនដំណឹង --}}
    <div class="card mb-6">
        <h2 lang="km" class="text-xl font-bold mb-4">សារជូនដំណឹង (Alert Messages)</h2>
        
        <div class="space-y-3">
            <div class="alert-brand-success">
                <i class="fas fa-check-circle"></i>
                <span lang="km">ទទួលការបង់ប្រាក់ដោយជោគជ័យ! (Payment received successfully!)</span>
            </div>
            
            <div class="alert-brand-error">
                <i class="fas fa-exclamation-circle"></i>
                <span lang="km">ការបង់ប្រាក់លើសកំណត់! (Payment is overdue!)</span>
            </div>
            
            <div class="alert-brand-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span lang="km">ជិតដល់កាលកំណត់ហើយ! (Due date approaching!)</span>
            </div>
            
            <div class="alert-brand-info">
                <i class="fas fa-info-circle"></i>
                <span lang="km">សូមត្រួតពិនិត្យព័ត៌មានរបស់អ្នក (Please review your information)</span>
            </div>
        </div>
    </div>

    {{-- ផ្នែកទី 4: Stat Cards - កាតស្ថិតិ --}}
    <div class="card mb-6">
        <h2 lang="km" class="text-xl font-bold mb-4">កាតស្ថិតិ (Stat Cards)</h2>
        
        <div class="stat-grid">
            {{-- ចំនួនទឹកប្រាក់បង់រួច (Total Paid) --}}
            <div class="stat-card sc-green">
                <div>
                    <div class="sc-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div lang="km" class="sc-label">ចំនួនទឹកប្រាក់បង់រួច</div>
                    <div class="sc-value">$125,430</div>
                    <div lang="km" class="sc-trend">↑ 15% ពីខែមុន</div>
                </div>
                <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                    <polyline points="0,18 40,12 80,15 120,8 160,14 200,5" fill="none" stroke="#fff" stroke-width="2"/>
                </svg>
            </div>

            {{-- ចំនួនទឹកប្រាក់លើសកំណត់ (Overdue) --}}
            <div class="stat-card sc-red">
                <div>
                    <div class="sc-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div lang="km" class="sc-label">ចំនួនទឹកប្រាក់លើសកំណត់</div>
                    <div class="sc-value">$8,240</div>
                    <div lang="km" class="sc-trend">↓ 5% ពីខែមុន</div>
                </div>
                <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                    <polyline points="0,24 40,18 80,22 120,14 160,20 200,10" fill="none" stroke="#fff" stroke-width="2"/>
                </svg>
            </div>

            {{-- ការបង់ប្រាក់កំពុងរង់ចាំ (Pending) --}}
            <div class="stat-card sc-amber">
                <div>
                    <div class="sc-icon"><i class="fas fa-clock"></i></div>
                    <div lang="km" class="sc-label">ការបង់ប្រាក់កំពុងរង់ចាំ</div>
                    <div class="sc-value">$32,650</div>
                    <div lang="km" class="sc-trend">↑ 8% ពីខែមុន</div>
                </div>
                <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                    <polyline points="0,20 40,16 80,18 120,12 160,16 200,8" fill="none" stroke="#fff" stroke-width="2"/>
                </svg>
            </div>

            {{-- អតិថិជនសកម្ម (Active Customers) --}}
            <div class="stat-card sc-blue">
                <div>
                    <div class="sc-icon"><i class="fas fa-users"></i></div>
                    <div lang="km" class="sc-label">អតិថិជនសកម្ម</div>
                    <div class="sc-value">245</div>
                    <div lang="km" class="sc-trend">↑ 12 នាក់ ខែនេះ</div>
                </div>
                <svg class="sc-wave" viewBox="0 0 200 36" preserveAspectRatio="none">
                    <polyline points="0,22 40,14 80,20 120,10 160,18 200,6" fill="none" stroke="#fff" stroke-width="2"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- ផ្នែកទី 5: Typography - ពុម្ពអក្សរ --}}
    <div class="card mb-6">
        <h2 lang="km" class="text-xl font-bold mb-4">ពុម្ពអក្សរ (Typography)</h2>
        
        <div class="space-y-4">
            {{-- ចំណងជើង (Headings) --}}
            <div>
                <h3 lang="km" class="text-lg font-semibold mb-2">ចំណងជើង (Headings)</h3>
                <h1 lang="km" class="text-4xl font-bold mb-1">ចំណងជើងទី 1 (Heading 1)</h1>
                <h2 lang="km" class="text-3xl font-bold mb-1">ចំណងជើងទី 2 (Heading 2)</h2>
                <h3 lang="km" class="text-2xl font-bold mb-1">ចំណងជើងទី 3 (Heading 3)</h3>
                <h4 lang="km" class="text-xl font-bold mb-1">ចំណងជើងទី 4 (Heading 4)</h4>
            </div>

            {{-- អត្ថបទ (Paragraphs) --}}
            <div>
                <h3 lang="km" class="text-lg font-semibold mb-2">អត្ថបទ (Paragraphs)</h3>
                <p lang="km" class="mb-2">
                    នេះជាអត្ថបទជាភាសាខ្មែរដែលប្រើពុម្ពអក្សរ SN-Kh-Menghorn។ 
                    ប្រព័ន្ធនេះត្រូវបានរចនាឡើងដើម្បីគាំទ្រទាំងភាសាអង់គ្លេសនិងខ្មែរ។
                </p>
                <p lang="en" class="mb-2">
                    This is English text using Times New Roman font. 
                    The system is designed to support both English and Khmer languages.
                </p>
                <p class="mixed-content">
                    This is mixed content អត្ថបទចម្រុះ with both languages ភាសាទាំងពីរ together.
                </p>
            </div>

            {{-- កម្រាស់ពុម្ពអក្សរ (Font Weights) --}}
            <div>
                <h3 lang="km" class="text-lg font-semibold mb-2">កម្រាស់ពុម្ពអក្សរ (Font Weights)</h3>
                <p lang="km" class="font-normal mb-1">ធម្មតា (Normal - 400)</p>
                <p lang="km" class="font-medium mb-1">មធ្យម (Medium - 500)</p>
                <p lang="km" class="font-semibold mb-1">ពាក់កណ្តាលធ្ងន់ (Semi Bold - 600)</p>
                <p lang="km" class="font-bold mb-1">ធ្ងន់ (Bold - 700)</p>
                <p lang="km" class="font-extrabold">ធ្ងន់ខ្លាំង (Extra Bold - 800)</p>
            </div>
        </div>
    </div>

    {{-- ផ្នែកទី 6: Table Example - ឧទាហរណ៍តារាង --}}
    <div class="card mb-6">
        <h2 lang="km" class="text-xl font-bold mb-4">តារាងការបង់ប្រាក់ (Payment Table)</h2>
        
        <table class="tbl">
            <thead>
                <tr>
                    <th lang="km">កាលបរិច្ឆេទ</th>
                    <th lang="km">អតិថិជន</th>
                    <th lang="km">ចំនួនទឹកប្រាក់</th>
                    <th lang="km">ស្ថានភាព</th>
                    <th lang="km">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2026-06-01</td>
                    <td lang="km">លី សុភា</td>
                    <td>$500.00</td>
                    <td><span class="pill pill-paid">បង់រួច</span></td>
                    <td>
                        <button class="btn-brand-blue" style="padding: 6px 12px; font-size: 12px;">
                            <i class="fas fa-eye"></i> មើល
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>2026-05-28</td>
                    <td lang="km">ចាន់ សុខា</td>
                    <td>$750.00</td>
                    <td><span class="pill pill-overdue">លើសកំណត់</span></td>
                    <td>
                        <button class="btn-brand-red" style="padding: 6px 12px; font-size: 12px;">
                            <i class="fas fa-bell"></i> រំលឹក
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>2026-06-05</td>
                    <td lang="km">ពេជ្រ វិទូ</td>
                    <td>$1,200.00</td>
                    <td><span class="pill pill-pending">កំពុងរង់ចាំ</span></td>
                    <td>
                        <button class="btn-brand-yellow" style="padding: 6px 12px; font-size: 12px;">
                            <i class="fas fa-clock"></i> រង់ចាំ
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>2026-06-03</td>
                    <td lang="km">សុខ មករា</td>
                    <td>$950.00</td>
                    <td><span class="pill pill-paid">បង់រួច</span></td>
                    <td>
                        <button class="btn-brand-blue" style="padding: 6px 12px; font-size: 12px;">
                            <i class="fas fa-eye"></i> មើល
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ផ្នែកទី 7: Color Palette - ក្រមពណ៌ --}}
    <div class="card">
        <h2 lang="km" class="text-xl font-bold mb-4">ក្រមពណ៌ (Color Palette)</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- Green --}}
            <div>
                <div class="w-full h-24 bg-green-600 rounded-lg mb-2 flex items-center justify-center text-white font-bold">
                    #059669
                </div>
                <h4 lang="km" class="font-semibold text-sm mb-1">🟢 បៃតង (Green)</h4>
                <p lang="km" class="text-xs text-gray-600">បង់រួច, ជោគជ័យ</p>
            </div>

            {{-- Red --}}
            <div>
                <div class="w-full h-24 bg-red-600 rounded-lg mb-2 flex items-center justify-center text-white font-bold">
                    #DC2626
                </div>
                <h4 lang="km" class="font-semibold text-sm mb-1">🔴 ក្រហម (Red)</h4>
                <p lang="km" class="text-xs text-gray-600">លើសកំណត់, កំហុស</p>
            </div>

            {{-- Yellow --}}
            <div>
                <div class="w-full h-24 bg-yellow-600 rounded-lg mb-2 flex items-center justify-center text-white font-bold">
                    #D97706
                </div>
                <h4 lang="km" class="font-semibold text-sm mb-1">🟡 លឿង (Yellow)</h4>
                <p lang="km" class="text-xs text-gray-600">រង់ចាំ, ការព្រមាន</p>
            </div>

            {{-- Blue --}}
            <div>
                <div class="w-full h-24 bg-blue-600 rounded-lg mb-2 flex items-center justify-center text-white font-bold">
                    #2563EB
                </div>
                <h4 lang="km" class="font-semibold text-sm mb-1">🔵 ខៀវ (Blue)</h4>
                <p lang="km" class="text-xs text-gray-600">ព័ត៌មាន, សកម្មភាព</p>
            </div>

            {{-- Gray --}}
            <div>
                <div class="w-full h-24 bg-gray-600 rounded-lg mb-2 flex items-center justify-center text-white font-bold">
                    #64748B
                </div>
                <h4 lang="km" class="font-semibold text-sm mb-1">⚫ ប្រផេះ (Gray)</h4>
                <p lang="km" class="text-xs text-gray-600">មិនសកម្ម, បិទ</p>
            </div>
        </div>
    </div>
</div>
@endsection

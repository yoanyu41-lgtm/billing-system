<!-- 4 Separate Report Navigation Tab Groups -->
<div class="flex flex-wrap items-center gap-3">
    
    <!-- Group 1: 📅 ប្រចាំ (Time-Based Summary) -->
    <div class="inline-flex items-center p-1 bg-slate-100/90 rounded-2xl border border-slate-200/80 shadow-2xs">
        <a href="{{ route('admin.reports.daily') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 {{ request()->routeIs('admin.reports.daily') ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fas fa-calendar-day"></i> {{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}
        </a>
        <a href="{{ route('admin.reports.monthly') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 {{ request()->routeIs('admin.reports.monthly') ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fas fa-calendar-days"></i> {{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}
        </a>
        <a href="{{ route('admin.reports.yearly') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 {{ request()->routeIs('admin.reports.yearly') ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fas fa-chart-line"></i> {{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}
        </a>
    </div>

    <!-- Group 2: 🧾 ការលក់ & ទូទាត់ (Sales & Payments) -->
    <div class="inline-flex items-center p-1 bg-slate-100/90 rounded-2xl border border-slate-200/80 shadow-2xs">
        <a href="{{ route('admin.reports.sales') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 {{ request()->routeIs('admin.reports.sales') ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fas fa-receipt"></i> {{ app()->getLocale() === 'km' ? 'ការលក់' : 'Sales' }}
        </a>
        <a href="{{ route('admin.reports.payment') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 {{ request()->routeIs('admin.reports.payment') ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fas fa-wallet"></i> {{ app()->getLocale() === 'km' ? 'ការទូទាត់' : 'Payments' }}
        </a>
    </div>

    <!-- Group 3: 📄 ការបង់រំលស់ (Installments) -->
    <div class="inline-flex items-center p-1 bg-slate-100/90 rounded-2xl border border-slate-200/80 shadow-2xs">
        <a href="{{ route('admin.reports.installment') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 {{ request()->routeIs('admin.reports.installment') ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fas fa-file-invoice-dollar"></i> {{ app()->getLocale() === 'km' ? 'បង់រំលស់' : 'Installments' }}
        </a>
    </div>

    <!-- Group 4: 📊 លម្អិត & ចំណាយ (Details & Expenses) -->
    <div class="inline-flex items-center p-1 bg-slate-100/90 rounded-2xl border border-slate-200/80 shadow-2xs">
        <a href="{{ route('admin.reports.customer') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 {{ request()->routeIs('admin.reports.customer') ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fas fa-users"></i> {{ app()->getLocale() === 'km' ? 'អតិថិជន' : 'Customers' }}
        </a>
        <a href="{{ route('admin.reports.product') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 {{ request()->routeIs('admin.reports.product') ? 'bg-white text-blue-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fas fa-boxes-stacked"></i> {{ app()->getLocale() === 'km' ? 'ផលិតផល' : 'Products' }}
        </a>
        <a href="{{ route('admin.reports.expense') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 {{ request()->routeIs('admin.reports.expense') ? 'bg-white text-rose-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
            <i class="fas fa-hand-holding-dollar text-rose-500"></i> {{ app()->getLocale() === 'km' ? 'ចំណាយ' : 'Expenses' }}
        </a>
    </div>

</div>

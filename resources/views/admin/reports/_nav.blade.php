<!-- Reports Navigation Bar (7 Structured Reports) -->
<div class="flex flex-wrap items-center gap-2 no-print">
    
    <!-- Sales Report -->
    <a href="{{ route('admin.reports.sales') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 border {{ request()->routeIs('admin.reports.sales') || request()->routeIs('admin.reports.daily') ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }}">
        <i class="fas fa-chart-bar"></i> {{ app()->getLocale() === 'km' ? 'ការលក់' : 'Sales' }}
    </a>

    <!-- Payment Report -->
    <a href="{{ route('admin.reports.payment') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 border {{ request()->routeIs('admin.reports.payment') ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }}">
        <i class="fas fa-wallet"></i> {{ app()->getLocale() === 'km' ? 'ការទូទាត់' : 'Payments' }}
    </a>

    <!-- Installment Report -->
    <a href="{{ route('admin.reports.installment') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 border {{ request()->routeIs('admin.reports.installment') ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }}">
        <i class="fas fa-file-invoice-dollar"></i> {{ app()->getLocale() === 'km' ? 'បង់រំលស់' : 'Installments' }}
    </a>

    <!-- Customer Report -->
    <a href="{{ route('admin.reports.customer') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 border {{ request()->routeIs('admin.reports.customer') ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }}">
        <i class="fas fa-users"></i> {{ app()->getLocale() === 'km' ? 'អតិថិជន' : 'Customers' }}
    </a>

    <!-- Product Report -->
    <a href="{{ route('admin.reports.product') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 border {{ request()->routeIs('admin.reports.product') ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }}">
        <i class="fas fa-boxes-stacked"></i> {{ app()->getLocale() === 'km' ? 'ផលិតផល' : 'Products' }}
    </a>

    <!-- Expense Report -->
    <a href="{{ route('admin.reports.expense') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 border {{ request()->routeIs('admin.reports.expense') ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }}">
        <i class="fas fa-hand-holding-dollar"></i> {{ app()->getLocale() === 'km' ? 'ចំណាយ' : 'Expenses' }}
    </a>

    <!-- Profit Report -->
    <a href="{{ route('admin.reports.profit') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition no-underline flex items-center gap-1.5 border {{ request()->routeIs('admin.reports.profit') || request()->routeIs('admin.reports.income') || request()->routeIs('admin.reports.monthly') || request()->routeIs('admin.reports.yearly') ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }}">
        <i class="fas fa-chart-line"></i> {{ app()->getLocale() === 'km' ? 'ប្រាក់ចំណេញ' : 'Profit / Income' }}
    </a>

</div>

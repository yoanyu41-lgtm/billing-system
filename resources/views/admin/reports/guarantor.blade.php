@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">

    <!-- Header Section -->
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍អ្នកធានា (Guarantors)' : 'Guarantor Report' }}
            </h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                {{ app()->getLocale() === 'km' ? 'បញ្ជីឈ្មោះអ្នកធានារបស់អតិថិជនបង់រំលស់នីមួយៗ ព័ត៌មានទំនាក់ទំនង និងស្ថានភាពកិច្ចសន្យា' : 'List of customer installment guarantors, contact details, and contract status' }}
            </p>
        </div>
        
        @include('admin.reports._nav')
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.guarantor', ['filter' => 'daily', 'search' => $search ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['today', 'daily']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំថ្ងៃ' : 'Daily' }}
            </a>
            <a href="{{ route('admin.reports.guarantor', ['filter' => 'this_week', 'search' => $search ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ ($filter ?? '') === 'this_week' ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំសប្តាហ៍' : 'This Week' }}
            </a>
            <a href="{{ route('admin.reports.guarantor', ['filter' => 'monthly', 'search' => $search ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_month', 'monthly']) || empty($filter) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំខែ' : 'Monthly' }}
            </a>
            <a href="{{ route('admin.reports.guarantor', ['filter' => 'yearly', 'search' => $search ?? '']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold transition no-underline border {{ in_array($filter ?? '', ['this_year', 'yearly']) ? 'bg-white border-blue-500 text-slate-900 shadow-sm font-bold ring-1 ring-blue-500' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ app()->getLocale() === 'km' ? 'ប្រចាំឆ្នាំ' : 'Yearly' }}
            </a>
        </div>

        <form method="GET" action="{{ route('admin.reports.guarantor') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="filter" value="custom">

            <div class="relative min-w-[200px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="{{ app()->getLocale() === 'km' ? 'ស្វែងរកឈ្មោះអ្នកធានា, អតិថិជន...' : 'Search guarantor, customer...' }}"
                       class="w-full px-3.5 py-2 pl-8 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
                <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <span>{{ app()->getLocale() === 'km' ? 'ចាប់ពី:' : 'From' }}</span>
                <input type="date" name="start_date" value="{{ $startDate ?? today()->toDateString() }}" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <span>{{ app()->getLocale() === 'km' ? 'ដល់:' : 'To' }}</span>
                <input type="date" name="end_date" value="{{ $endDate ?? today()->toDateString() }}" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs">
            </div>

            <button type="submit" class="px-4 py-2 bg-[#0b1f3a] hover:bg-[#07162b] text-white font-bold rounded-xl text-xs transition shadow-sm border-0 cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-search text-[11px]"></i> {{ app()->getLocale() === 'km' ? 'ស្វែងរក' : 'Search' }}
            </button>

            @if($search || request('start_date'))
            <a href="{{ route('admin.reports.guarantor') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold no-underline">
                {{ app()->getLocale() === 'km' ? 'សម្អាត' : 'Clear' }}
            </a>
            @endif

            <button type="button" onclick="printReportDirect('{{ route('admin.reports.print', ['type' => 'guarantor', 'search' => $search ?? '', 'start_date' => $startDate ?? '', 'end_date' => $endDate ?? '', 'filter' => $filter ?? '']) }}')" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-200 shadow-xs cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                <i class="fas fa-print text-slate-500"></i> {{ app()->getLocale() === 'km' ? 'បោះពុម្ព' : 'Print' }}
            </button>
        </form>
    </div>

    <!-- Summary Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <!-- Total Guarantors -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/20 text-lg">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'អ្នកធានាសរុប' : 'TOTAL GUARANTORS' }}</span>
                <div class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($totalGuarantors) }} នាក់</div>
            </div>
        </div>

        <!-- Active Guaranteed Contracts -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/20 text-lg">
                <i class="fas fa-file-contract"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'កិច្ចសន្យាកំពុងធានា' : 'ACTIVE GUARANTEED' }}</span>
                <div class="text-xl font-black text-emerald-600 mt-0.5">{{ number_format($activeGuaranteedContracts) }}</div>
            </div>
        </div>

        <!-- Overdue Guaranteed Contracts -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-rose-500/20 text-lg">
                <i class="fas fa-phone-volume"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">{{ app()->getLocale() === 'km' ? 'កិច្ចសន្យាយឺតត្រូវទាក់ទង' : 'OVERDUE TO CONTACT' }}</span>
                <div class="text-xl font-black text-rose-600 mt-0.5">{{ number_format($overdueGuaranteedContracts) }}</div>
            </div>
        </div>

    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800">
                {{ app()->getLocale() === 'km' ? 'បញ្ជីឈ្មោះអ្នកធានា' : 'Guarantor Directory' }}
            </h2>
            <span class="text-xs text-slate-500 font-semibold">
                {{ app()->getLocale() === 'km' ? 'ទំព័រទី:' : 'Page:' }} {{ $guarantors->currentPage() }} / {{ $guarantors->lastPage() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 font-semibold">#</th>
                        <th class="py-3 px-4 font-semibold">{{ app()->getLocale() === 'km' ? 'ឈ្មោះអ្នកធានា' : 'Guarantor Name' }}</th>
                        <th class="py-3 px-4 font-semibold">{{ app()->getLocale() === 'km' ? 'ទំនាក់ទំនង' : 'Contact' }}</th>
                        <th class="py-3 px-4 font-semibold">{{ app()->getLocale() === 'km' ? 'ត្រូវជាជាមួយអតិថិជន' : 'Relationship' }}</th>
                        <th class="py-3 px-4 font-semibold">{{ app()->getLocale() === 'km' ? 'អតិថិជនដែលបានធានា' : 'Guaranteed Customer' }}</th>
                        <th class="py-3 px-4 font-semibold text-center">{{ app()->getLocale() === 'km' ? 'កិច្ចសន្យាកំពុងដំណើរការ' : 'Active Contracts' }}</th>
                        <th class="py-3 px-4 font-semibold text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($guarantors as $g)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-400">
                            #G-{{ str_pad($g->id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900">{{ $g->name }}</div>
                            @if($g->id_card)
                                <div class="text-[10px] text-slate-400 font-mono">ID: {{ $g->id_card }}</div>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="text-[12px] text-slate-600 font-medium">
                                {{ $g->phone ?? '-' }}
                            </div>
                            @if($g->address)
                                <div class="text-[11px] text-slate-400 truncate max-w-[200px]" title="{{ $g->address }}">
                                    <i class="fas fa-map-marker-alt text-[9px]"></i> {{ $g->address }}
                                </div>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            <span class="px-2 py-0.5 rounded-md text-[11px] bg-slate-100 text-slate-700">
                                {{ $g->relationship ?: 'អ្នកធានា' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            @if($g->customer)
                                <div class="font-bold text-slate-900">{{ $g->customer->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $g->customer->phone }}</div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @php
                                $activeInstCount = $g->customer?->installments?->count() ?? 0;
                            @endphp
                            @if($activeInstCount > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    {{ $activeInstCount }} កិច្ចសន្យា
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px]">គ្មាន</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            @if($g->customer)
                            <a href="{{ route('customers.show', $g->customer) }}" 
                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                <i class="fas fa-user text-[10px]"></i>
                                <span>{{ __('app.view') }}</span>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fas fa-user-shield text-2xl block mb-2 opacity-40"></i>
                            <span>{{ app()->getLocale() === 'km' ? 'មិនមានទិន្នន័យអ្នកធានាឡើយ' : 'No guarantors found' }}</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-slate-100">
            {{ $guarantors->links() }}
        </div>
    </div>

</div>
@endsection

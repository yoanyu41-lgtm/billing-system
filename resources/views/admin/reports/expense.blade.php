@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl space-y-8">

    <!-- Header Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-rose-600 mb-1">
                <i class="fas fa-hand-holding-dollar"></i> {{ __('app.reports') }}
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'km' ? 'របាយការណ៍ចំណាយ' : 'Expense Report' }}
            </h1>
            <p class="text-sm font-semibold text-slate-500 mt-1 font-mono">
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </p>
        </div>
        @include('admin.reports._nav')
    </div>

    <!-- Header & Action Controls -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 no-print">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('addExpenseModal').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition border-0 cursor-pointer shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-plus"></i> Record Expense
                </button>

                <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition border-0 cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-print text-slate-500"></i> Print
                </button>
            </div>

            <form method="GET" action="{{ route('admin.reports.expense') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="text-xs font-medium text-slate-400">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-xs border-0 cursor-pointer flex items-center justify-center">
                    Filter
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-sm font-bold flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Cards (5 Summary Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Expense (Red Highlight) -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0 border border-rose-100">
                <i class="fas fa-calculator"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Total Expenses</span>
                <span class="text-xl font-black text-rose-600">${{ number_format($totalExpenses, 2) }}</span>
            </div>
        </div>

        <!-- Office Expense -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0 border border-blue-100">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Office Expense</span>
                <span class="text-xl font-black text-slate-900">${{ number_format($officeExpense, 2) }}</span>
            </div>
        </div>

        <!-- Salary -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0 border border-emerald-100">
                <i class="fas fa-user-group"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Salary</span>
                <span class="text-xl font-black text-slate-900">${{ number_format($salaryExpense, 2) }}</span>
            </div>
        </div>

        <!-- Utility -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0 border border-amber-100">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Utility</span>
                <span class="text-xl font-black text-slate-900">${{ number_format($utilityExpense, 2) }}</span>
            </div>
        </div>

        <!-- Other -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0 border border-purple-100">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Other Expenses</span>
                <span class="text-xl font-black text-slate-900">${{ number_format($otherExpense, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fas fa-list-check text-rose-500"></i> Expense Records
            </h3>
            <span class="text-sm font-black text-rose-600">
                Total: ${{ number_format($totalExpenses, 2) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">Expense ID</th>
                        <th class="py-3.5 px-6">Category</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6 text-right">Amount</th>
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6">Paid By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($expenses as $exp)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-bold text-slate-900 font-mono text-xs">
                            EXP-{{ str_pad($exp->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-800">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-bold text-slate-700">
                                {{ $exp->category }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-600">
                            {{ $exp->description ?? '-' }}
                        </td>
                        <td class="py-4 px-6 text-right font-black text-rose-600">
                            ${{ number_format($exp->amount, 2) }}
                        </td>
                        <td class="py-4 px-6 text-xs font-mono text-slate-500">
                            {{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 font-medium">
                            <i class="fas fa-user-shield text-blue-500 mr-1 text-[10px]"></i>{{ $exp->user->name ?? 'Admin' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                            No Expense Records Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Add Expense -->
<div id="addExpenseModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full p-6 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-900 text-lg flex items-center gap-2">
                <i class="fas fa-plus-circle text-rose-500"></i> Record New Expense
            </h3>
            <button onclick="document.getElementById('addExpenseModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 border-0 bg-transparent cursor-pointer text-base">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Category</label>
                <select name="category" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800">
                    <option value="Office Expense">Office Expense</option>
                    <option value="Salary">Salary</option>
                    <option value="Utility">Electricity / Utility</option>
                    <option value="Transport">Transport / Delivery</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Amount ($)</label>
                <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Date</label>
                <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Description details..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addExpenseModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm border-0 cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-sm border-0 cursor-pointer shadow-sm">
                    Save Expense
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

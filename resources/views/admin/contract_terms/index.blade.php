@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.contract_terms') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.contract_terms_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.contract-terms.create') }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-sm transition duration-150">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            {{ __('app.add_term') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3.5 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm text-sm font-medium">
            <svg class="w-5 h-5 mr-2.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider w-20">#</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">{{ __('app.term_title') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider w-36">{{ __('app.status') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-600 uppercase tracking-wider w-36">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($terms as $term)
                    <tr class="hover:bg-slate-50/70 transition duration-150">
                        <td class="px-6 py-5 text-center text-base text-slate-700 font-bold">{{ $term->sort_order }}</td>
                        <td class="px-6 py-5">
                            <div class="text-base font-bold text-slate-900 leading-snug">{{ $term->title_km }}</div>
                            @if($term->title_en)
                                <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mt-0.5">{{ $term->title_en }}</div>
                            @endif
                            <div class="text-sm text-slate-600 mt-1.5 leading-relaxed">{{ \Illuminate\Support\Str::limit($term->content_km, 120) }}</div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            @if($term->is_active)
                                <span class="px-3.5 py-1 inline-flex text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fas fa-check-circle mr-1 self-center"></i> {{ __('app.active') }}
                                </span>
                            @else
                                <span class="px-3.5 py-1 inline-flex text-xs font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                    <i class="fas fa-pause-circle mr-1 self-center"></i> {{ __('app.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center gap-2">
                                <a href="{{ route('admin.contract-terms.edit', $term) }}" class="p-2.5 text-amber-600 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition duration-150 shadow-2xs" title="{{ __('app.edit') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.contract-terms.destroy', $term) }}" class="inline-block" onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition duration-150 shadow-2xs cursor-pointer" title="{{ __('app.delete') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 font-medium">{{ __('app.no_terms') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

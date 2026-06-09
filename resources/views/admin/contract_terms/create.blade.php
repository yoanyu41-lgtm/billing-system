@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('app.add_term') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.contract_terms_subtitle') }}</p>
        </div>
        <a href="{{ route('admin.contract-terms.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('app.back') }}
        </a>
    </div>

    <form method="POST" action="{{ route('admin.contract-terms.store') }}" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        @csrf
        @include('admin.contract_terms._form', ['term' => null])
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.contract-terms.index') }}" class="px-6 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm">{{ __('app.cancel') }}</a>
            <button type="submit" class="px-6 py-2.5 font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">{{ __('app.save') }}</button>
        </div>
    </form>
</div>
@endsection

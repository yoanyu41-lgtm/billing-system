@extends('layouts.app')

@section('content')
@php $isDirect = $customer->type === 'direct'; @endphp
<div class="{{ $isDirect ? 'max-w-2xl' : 'max-w-4xl' }} mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('customers.index', ['type' => $customer->type]) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">{{ __('app.edit_customer') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $customer->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('customers.update', $customer) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        {{-- ១. ព័ត៌មានផ្ទាល់ខ្លួន --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">១</span>
                {{ __('app.personal_information') }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.full_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                @if(!$isDirect)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.gender') }}</label>
                    <select name="gender" class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('app.select') }}</option>
                        <option value="male"   {{ old('gender', $customer->gender) === 'male'   ? 'selected' : '' }}>{{ __('app.male') }}</option>
                        <option value="female" {{ old('gender', $customer->gender) === 'female' ? 'selected' : '' }}>{{ __('app.female') }}</option>
                        <option value="other"  {{ old('gender', $customer->gender) === 'other'  ? 'selected' : '' }}>{{ __('app.other_gender') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.dob') }}</label>
                    <input type="date" name="dob" value="{{ old('dob', $customer->dob?->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.id_card') }}</label>
                    <input type="text" name="id_card" value="{{ old('id_card', $customer->id_card) }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.telegram_id') }}</label>
                    <input type="text" name="telegram_id" value="{{ old('telegram_id', $customer->telegram_id) }}"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endif

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('app.address') }}</label>
                    <textarea name="address" rows="2"
                              class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('address', $customer->address) }}</textarea>
                </div>
            </div>
        </div>

        @if(!$isDirect)
        {{-- ២. រូបថតអតិថិជន --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-5 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">២</span>
                {{ __('app.photo') }}
            </h2>
            <div class="flex items-center gap-6">
                <div id="photo-preview" class="w-24 h-24 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($customer->photo)
                        <img src="{{ asset('storage/' . $customer->photo) }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <label class="cursor-pointer inline-flex items-center gap-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        {{ $customer->photo ? __('app.change_photo') : __('app.upload_photo') }}
                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewImage(this, 'photo-preview')">
                    </label>
                    <p class="text-xs text-gray-400 mt-1.5">JPG, PNG {{ __('app.file_too_large') === 'ឯកសារធំពេក' ? 'រហូតដល់' : 'up to' }} 2MB</p>
                </div>
            </div>
        </div>

        {{-- ៣. ឯកសារ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-5 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">៣</span>
                {{ __('app.documents') }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @php
                $docs = [
                    ['name' => 'id_card_photo', 'label' => __('app.id_card_photo'), 'icon' => '🪪'],
                    ['name' => 'income_proof',  'label' => __('app.income_proof'),  'icon' => '💰'],
                ];
                @endphp
                @foreach($docs as $doc)
                <div class="border border-gray-100 rounded-lg p-3 bg-gray-50">
                    <p class="text-xs font-medium text-gray-600 mb-2">{{ $doc['icon'] }} {{ $doc['label'] }}</p>

                    {{-- Preview Area --}}
                    @php
                        $existing = $customer->{$doc['name']};
                        $ext = $existing ? strtolower(pathinfo($existing, PATHINFO_EXTENSION)) : '';
                        $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                    @endphp
                    <div id="preview-{{ $doc['name'] }}"
                         class="w-full h-32 rounded-lg border-2 border-dashed border-gray-200 bg-white flex flex-col items-center justify-center gap-1 mb-2 overflow-hidden cursor-pointer relative group"
                         onclick="document.getElementById('file-{{ $doc['name'] }}').click()">

                        @if($existing && $isImg)
                            {{-- บangkok existing image --}}
                            <img id="preview-img-{{ $doc['name'] }}"
                                 src="{{ asset('storage/' . $existing) }}"
                                 class="absolute inset-0 w-full h-full object-cover rounded-lg">
                            <div id="preview-placeholder-{{ $doc['name'] }}" class="hidden"></div>
                        @elseif($existing && !$isImg)
                            {{-- PDF existing --}}
                            <div id="preview-placeholder-{{ $doc['name'] }}" class="flex flex-col items-center gap-1">
                                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs text-red-500 font-medium">PDF</span>
                            </div>
                            <img id="preview-img-{{ $doc['name'] }}" src="" class="hidden absolute inset-0 w-full h-full object-cover rounded-lg">
                        @else
                            <div id="preview-placeholder-{{ $doc['name'] }}" class="flex flex-col items-center gap-1">
                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span class="text-xs text-gray-400">{{ __('app.upload') }}</span>
                            </div>
                            <img id="preview-img-{{ $doc['name'] }}" src="" class="hidden absolute inset-0 w-full h-full object-cover rounded-lg">
                        @endif

                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors rounded-lg"></div>
                    </div>

                    <input type="file" id="file-{{ $doc['name'] }}" name="{{ $doc['name'] }}" accept="image/*,.pdf"
                           class="hidden" onchange="previewDoc(this, '{{ $doc['name'] }}')">
                    <p class="text-xs text-gray-400 truncate" id="fn-{{ $doc['name'] }}">
                        {{ $existing ? basename($existing) : __('app.no_file_chosen') }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Buttons --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('customers.show', $customer) }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                {{ __('app.cancel') }}
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                {{ __('app.update') }}
            </button>
        </div>

    </form>

    @if($isDirect && $sales->count())
    {{-- ── Purchase history (read-only) ── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">{{ __('app.sales_list') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.invoice_no') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.sale_date') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.product') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.total') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sales as $sale)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-5 py-3 font-semibold text-blue-600 whitespace-nowrap">{{ $sale->invoice_no ?? ('#'.$sale->id) }}</td>
                        <td class="px-5 py-3 text-gray-600 whitespace-nowrap">{{ optional($sale->sale_date)->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-gray-700">
                            @foreach($sale->items as $item)
                                <div class="flex items-center gap-2 {{ !$loop->last ? 'mb-1' : '' }}">
                                    <span>{{ $item->product->name ?? '—' }}</span>
                                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">x{{ $item->quantity }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-gray-800 whitespace-nowrap">${{ number_format($sale->total, 2) }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.sales.show', $sale) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                <i class="fas fa-eye"></i> {{ __('app.view_receipt') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewDoc(input, name) {
    const fn  = document.getElementById('fn-' + name);
    const img = document.getElementById('preview-img-' + name);
    const ph  = document.getElementById('preview-placeholder-' + name);

    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    if (fn) fn.textContent = file.name;

    if (file.type.startsWith('image/') && img) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.classList.remove('hidden');
            if (ph) ph.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    } else if (ph) {
        img && img.classList.add('hidden');
        ph.classList.remove('hidden');
        ph.innerHTML = `<svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="text-xs text-red-500 font-medium">PDF</span>`;
    }
}
</script>
@endsection

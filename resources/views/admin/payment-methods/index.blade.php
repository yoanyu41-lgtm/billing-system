@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Payment Methods</h1>
        <p class="text-sm text-slate-600">Manage the payment choices used by the billing system.</p>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-xl bg-white p-6 shadow lg:col-span-1">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">Add New Method</h2>
        <form method="POST" action="{{ route('admin.payment-methods.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                <input type="text" name="name" required class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="e.g. QR">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Details</label>
                <textarea name="details" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Optional details"></textarea>
            </div>
            <button type="submit" class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Save Method</button>
        </form>
    </div>

    <div class="rounded-xl bg-white p-6 shadow lg:col-span-2">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">Available Methods</h2>
        <div class="space-y-4">
            @forelse($paymentMethods as $paymentMethod)
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                        <form method="POST" action="{{ route('admin.payment-methods.update', $paymentMethod) }}" class="grid gap-3 md:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Method Name</label>
                                <input type="text" name="name" value="{{ $paymentMethod->name }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Details</label>
                                <input type="text" name="details" value="{{ $paymentMethod->details }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Update</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('admin.payment-methods.destroy', $paymentMethod) }}" onsubmit="return confirm('Delete this payment method?')" class="md:justify-self-end">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-slate-500">No payment methods found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

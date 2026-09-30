<x-admin-layout :title="$coupon ? 'Edit Coupon' : 'New Coupon'">
    <div class="max-w-xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $coupon ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="space-y-4">
            @csrf
            @if ($coupon) @method('PUT') @endif

            @if (!$coupon)
                <x-admin.field name="code" label="Code (e.g. ROSADO10)" required />
            @else
                <div>
                    <p class="block text-xs font-medium uppercase tracking-wider text-neutral-500">Code</p>
                    <p class="mt-1.5 font-mono text-sm text-neutral-800">{{ $coupon->code }}</p>
                </div>
            @endif
            <x-admin.select name="type" label="Type" :options="['PERCENT' => 'Percent', 'FLAT' => 'Flat']" :selected="$coupon?->type ?? 'PERCENT'" required />
            <x-admin.field name="value" label="Value" type="number" step="0.01" :value="$coupon?->value ?? 0" required />
            <x-admin.field name="min_subtotal" label="Minimum Subtotal (₹)" type="number" step="0.01" :value="$coupon?->min_subtotal ?? 0" required />
            <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$coupon?->status ?? 'ACTIVE'" required />

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.coupons.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>

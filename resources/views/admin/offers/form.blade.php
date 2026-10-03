<x-admin-layout :title="$offer ? 'Edit Offer' : 'New Offer'">
    <div class="max-w-xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $offer ? route('admin.offers.update', $offer) : route('admin.offers.store') }}" class="space-y-4">
            @csrf
            @if ($offer) @method('PUT') @endif

            <x-admin.field name="text" label="Offer Text (e.g. Free shipping on orders above ₹1999)" :value="$offer?->text" maxlength="255" required />
            <x-admin.field name="link_url" label="Link (optional, e.g. /shop or a full URL)" :value="$offer?->link_url" maxlength="255" />
            <x-admin.field name="sort_order" label="Sort Order" type="number" :value="$offer?->sort_order ?? 0" required />
            <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$offer?->status ?? 'ACTIVE'" required />

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.offers.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>

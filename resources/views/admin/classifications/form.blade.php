<x-admin-layout :title="$classification ? 'Edit Classification' : 'New Classification'">
    <div class="max-w-xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $classification ? route('admin.classifications.update', $classification) : route('admin.classifications.store') }}" class="space-y-4">
            @csrf
            @if ($classification) @method('PUT') @endif

            @if (!$classification)
                <x-admin.field name="id" label="ID (e.g. FAM-CITRUS)" required />
            @endif
            <x-admin.select name="group" label="Group" :options="$groups" :selected="$classification?->group" blank="Select a group" required />
            <x-admin.field name="name" label="Name" :value="$classification?->name" required />
            <x-admin.field name="slug" label="Slug" :value="$classification?->slug" required />
            <x-admin.field name="sort_order" label="Sort Order" type="number" :value="$classification?->sort_order ?? 0" required />
            <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$classification?->status ?? 'ACTIVE'" required />

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.classifications.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>

@props(['title', 'createRoute' => null, 'createLabel' => 'Add New'])
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-lg font-medium text-neutral-800">{{ $title }}</h2>
    @if ($createRoute)
        <a href="{{ $createRoute }}" class="rounded-md bg-neutral-900 px-4 py-2 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">
            {{ $createLabel }}
        </a>
    @endif
</div>

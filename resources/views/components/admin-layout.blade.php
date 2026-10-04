@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="en" class="h-full bg-neutral-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · ROSADO Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
<div class="flex h-full min-h-screen bg-neutral-100">
    {{-- Sidebar --}}
    <aside class="flex w-64 shrink-0 flex-col bg-neutral-950 text-neutral-300">
        <div class="flex h-16 items-center gap-2 border-b border-white/10 px-6">
            <span class="font-serif text-xl tracking-[0.2em] text-white">ROSADO</span>
            <span class="rounded-full bg-amber-400/10 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider text-amber-300">Admin</span>
        </div>
        <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6 text-sm">
            @php
                $nav = [
                    'Overview' => [
                        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
                    ],
                    'Catalog' => [
                        ['label' => 'Products', 'route' => 'admin.products.index', 'pattern' => 'admin.products.*'],
                        ['label' => 'Fragrances', 'route' => 'admin.fragrances.index', 'pattern' => 'admin.fragrances.*'],
                        ['label' => 'Bottles', 'route' => 'admin.bottles.index', 'pattern' => 'admin.bottles.*'],
                        ['label' => 'Caps', 'route' => 'admin.caps.index', 'pattern' => 'admin.caps.*'],
                        ['label' => 'Alignment Tool', 'route' => 'admin.customizer.alignment', 'pattern' => 'admin.customizer.*'],
                        ['label' => 'Sizes', 'route' => 'admin.sizes.index', 'pattern' => 'admin.sizes.*'],
                        ['label' => 'Classifications', 'route' => 'admin.classifications.index', 'pattern' => 'admin.classifications.*'],
                    ],
                    'Sales' => [
                        ['label' => 'Orders', 'route' => 'admin.orders.index', 'pattern' => 'admin.orders.*'],
                        ['label' => 'Coupons', 'route' => 'admin.coupons.index', 'pattern' => 'admin.coupons.*'],
                        ['label' => 'Shipping Methods', 'route' => 'admin.shipping-methods.index', 'pattern' => 'admin.shipping-methods.*'],
                    ],
                    'Content' => [
                        ['label' => 'Offer Header', 'route' => 'admin.offers.index', 'pattern' => 'admin.offers.*'],
                    ],
                    'People' => [
                        ['label' => 'Customers', 'route' => 'admin.users.index', 'pattern' => 'admin.users.*'],
                    ],
                ];
            @endphp
            @foreach ($nav as $group => $items)
                <div>
                    <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-neutral-500">{{ $group }}</p>
                    <div class="mt-2 space-y-0.5">
                        @foreach ($items as $item)
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center rounded-md px-3 py-2 transition-colors {{ request()->routeIs($item['pattern']) ? 'bg-white/10 text-white' : 'text-neutral-400 hover:bg-white/5 hover:text-white' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
        <div class="border-t border-white/10 px-4 py-4">
            <a href="{{ config('app.frontend_url') }}" target="_blank" class="block rounded-md px-3 py-2 text-xs text-neutral-400 hover:bg-white/5 hover:text-white">
                ↗ View storefront
            </a>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="flex h-16 shrink-0 items-center justify-between border-b border-neutral-200 bg-white px-8">
            <h1 class="font-serif text-2xl text-neutral-900">{{ $title }}</h1>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-medium text-neutral-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-neutral-500">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md border border-neutral-300 px-3 py-1.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">
                        Sign out
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-8 py-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>

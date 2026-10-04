@php
    // Storefront palette + fonts. Background film is served by this Laravel app (public/videos).
    $video = asset('videos/ROSADO_PERFUME.mp4');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · ROSADO Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .rs-display { font-family: 'Cormorant Garamond', Georgia, serif; }
        .rs-body { font-family: 'Outfit', system-ui, sans-serif; }
        .rs-rule { height: 1px; background: linear-gradient(90deg, transparent, #D9CFC3, transparent); }
        .rs-input:focus { border-color: #1A1614; box-shadow: 0 0 0 1px #1A1614; outline: none; }
        @keyframes rs-rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .rs-rise { animation: rs-rise .8s ease-out both; }
        .rs-video { transition: opacity 1.2s ease; }
        @media (prefers-reduced-motion: reduce) { .rs-rise { animation: none; } }
    </style>
</head>
<body class="rs-body h-full min-h-screen bg-[#1A1614] text-[#1A1614] antialiased">

    {{-- Full-screen background film (fades in once it can play; charcoal until then). --}}
    <div class="fixed inset-0 -z-0 overflow-hidden" aria-hidden="true">
        <video data-bg-video class="rs-video absolute inset-0 h-full w-full object-cover opacity-0"
               muted loop playsinline preload="auto" autoplay>
            <source src="{{ $video }}" type="video/mp4">
        </video>
        {{-- Overlay keeps the card readable over any frame. --}}
        {{-- <div class="absolute inset-0 bg-[#1A1614]/55"></div> --}}
        {{-- <div class="absolute inset-0" style="background: radial-gradient(70% 60% at 50% 50%, rgba(26,22,20,.15), rgba(26,22,20,.65));"></div> --}}
    </div>

    <main class="relative z-10 flex min-h-screen flex-col items-center justify-center px-5 py-10">
        <div class="mb-8 text-center rs-rise">
            <span class="rs-display text-4xl tracking-[0.28em] text-[#F7F3EE]">ROSADO</span>
            <p class="mt-2 text-[11px] uppercase tracking-[0.16em] text-[#C4A484]">Admin dashboard</p>
        </div>

        <div class="w-full max-w-md rounded-3xl border border-white/20 bg-[#F7F3EE]/95 p-8 shadow-[0_30px_80px_-30px_rgba(0,0,0,.6)] backdrop-blur-md sm:p-10 rs-rise" style="animation-delay: .1s;">
            <div class="text-center">
                <h1 class="rs-display text-5xl leading-none">Welcome back</h1>
                <p class="mt-3 text-sm text-[#8A8178]">Sign in to manage perfumes, orders and the atelier.</p>
            </div>

            <div class="rs-rule my-7"></div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-[#C9A08A]/50 bg-[#C9A08A]/10 px-4 py-3 text-sm text-[#2C2622]" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-[11px] uppercase tracking-[0.16em] text-[#8A8178]">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           placeholder="Enter your email"
                           class="rs-input mt-2 block w-full rounded-xl border border-[#D9CFC3] bg-white/70 px-4 py-3 text-sm text-[#1A1614] placeholder-[#C8BDB0] transition">
                </div>
                <div>
                    <label for="password" class="block text-[11px] uppercase tracking-[0.16em] text-[#8A8178]">Password</label>
                    <div class="relative mt-2">
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               class="rs-input block w-full rounded-xl border border-[#D9CFC3] bg-white/70 px-4 py-3 pr-16 text-sm text-[#1A1614] transition">
                        <button type="button" data-toggle-password
                                class="absolute inset-y-0 right-0 px-4 text-[11px] uppercase tracking-[0.16em] text-[#8A8178] hover:text-[#1A1614]"
                                aria-controls="password" aria-label="Show password">Show</button>
                    </div>
                </div>
                <label class="flex items-center gap-2.5 text-sm text-[#2C2622]">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[#D9CFC3] text-[#1A1614] focus:ring-[#B8956A]">
                    Remember me
                </label>
                <button type="submit"
                        class="w-full rounded-full bg-[#1A1614] px-6 py-3.5 text-[12px] uppercase tracking-[0.16em] text-[#F7F3EE] transition hover:bg-[#2C2622] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#B8956A] focus-visible:ring-offset-2 focus-visible:ring-offset-[#F7F3EE]">
                    Sign in
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-xs text-[#EFE8DF]/80 rs-rise" style="animation-delay: .2s;">
            <a href="{{ config('app.frontend_url') }}" class="underline decoration-white/30 underline-offset-4 hover:text-white">← Back to the storefront</a>
            <span class="mx-2 text-white/30">·</span>
            © {{ date('Y') }} ROSADO Perfume
        </p>
    </main>

<script>
    (function () {
        const video = document.querySelector('[data-bg-video]');
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduceMotion) {
            // Respect reduced motion: show a still first frame instead of a looping film.
            video.removeAttribute('autoplay');
            video.pause();
            video.addEventListener('loadeddata', () => video.classList.remove('opacity-0'), { once: true });
        } else {
            video.addEventListener('playing', () => video.classList.remove('opacity-0'), { once: true });
            video.play?.().catch(() => {});
        }
        // Missing / blocked video: stay on the charcoal background.
        video.addEventListener('error', () => video.remove(), true);
    })();

    document.querySelector('[data-toggle-password]').addEventListener('click', (event) => {
        const input = document.getElementById('password');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        event.currentTarget.textContent = show ? 'Hide' : 'Show';
        event.currentTarget.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
</script>
</body>
</html>

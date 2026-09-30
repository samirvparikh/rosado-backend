<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · ROSADO Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex h-full min-h-screen items-center justify-center bg-neutral-950 font-sans antialiased">
    <div class="w-full max-w-sm">
        <div class="mb-8 text-center">
            <span class="font-serif text-3xl tracking-[0.3em] text-white">ROSADO</span>
            <p class="mt-2 text-xs uppercase tracking-[0.2em] text-amber-300">Admin Dashboard</p>
        </div>
        <div class="rounded-lg border border-white/10 bg-neutral-900 p-8 shadow-xl">
            @if ($errors->any())
                <div class="mb-5 rounded-md border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                    {{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-medium uppercase tracking-wider text-neutral-400">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="mt-1.5 block w-full rounded-md border border-white/10 bg-neutral-800 px-3 py-2.5 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                </div>
                <div>
                    <label for="password" class="block text-xs font-medium uppercase tracking-wider text-neutral-400">Password</label>
                    <input id="password" name="password" type="password" required
                           class="mt-1.5 block w-full rounded-md border border-white/10 bg-neutral-800 px-3 py-2.5 text-sm text-white placeholder-neutral-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                </div>
                <label class="flex items-center gap-2 text-xs text-neutral-400">
                    <input type="checkbox" name="remember" class="rounded border-white/20 bg-neutral-800 text-amber-400 focus:ring-amber-400">
                    Remember me
                </label>
                <button type="submit"
                        class="w-full rounded-md bg-amber-400 px-4 py-2.5 text-sm font-medium uppercase tracking-wider text-neutral-950 transition hover:bg-amber-300">
                    Sign in
                </button>
            </form>
        </div>
    </div>
</body>
</html>

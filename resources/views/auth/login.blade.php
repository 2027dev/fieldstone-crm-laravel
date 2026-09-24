<x-layouts.guest title="Log in">
    <h1 class="text-[28px] font-bold text-ink-900">Log in to Fieldstone</h1>
    <p class="mt-2 text-[15px] text-ink-700">Welcome back. Pick up where your pipeline left off.</p>

    @if ($errors->any())
        <div class="mt-5 rounded-lg border border-danger/25 bg-red-50 px-4 py-3 text-sm text-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-4">
        @csrf
        <div>
            <label class="field-label" for="login-email">Email</label>
            <input id="login-email" name="email" type="email" class="field" required autofocus value="{{ old('email') }}">
        </div>
        <div>
            <label class="field-label" for="login-password">Password</label>
            <input id="login-password" name="password" type="password" class="field" required autocomplete="current-password">
        </div>
        <label class="flex items-center gap-2 text-sm text-ink-700">
            <input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-indigo-brand"> Remember me
        </label>

        <button type="submit" class="btn-primary w-full">Log in</button>
    </form>

    <p class="mt-6 text-sm text-ink-700">
        New to Fieldstone? <a href="{{ route('register') }}" class="font-semibold text-link hover:underline">Create an account</a>
    </p>

    <div class="mt-8 rounded-xl border border-line bg-gray-50 px-4 py-3.5">
        <p class="text-[12px] font-bold tracking-wide text-ink-500 uppercase">Demo workspace</p>
        <p class="mt-1 text-sm text-ink-700">
            <span class="font-semibold">demo@fieldstonecrm.test</span> · password: <span class="font-semibold">password</span>
        </p>
    </div>
</x-layouts.guest>

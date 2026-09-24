<x-layouts.guest title="Create your account">
    <h1 class="text-[28px] font-bold text-ink-900">Create your workspace</h1>
    <p class="mt-2 text-[15px] text-ink-700">Start with sample data, then make it yours in a few minutes.</p>

    @if ($errors->any())
        <ul class="mt-5 list-inside list-disc rounded-lg border border-danger/25 bg-red-50 px-4 py-3 text-sm text-danger">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-4">
        @csrf
        <div>
            <label class="field-label" for="reg-name">Your name</label>
            <input id="reg-name" name="name" class="field" required autofocus value="{{ old('name') }}">
        </div>
        <div>
            <label class="field-label" for="reg-company">Company</label>
            <input id="reg-company" name="company_name" class="field" value="{{ old('company_name') }}">
        </div>
        <div>
            <label class="field-label" for="reg-email">Work email</label>
            <input id="reg-email" name="email" type="email" class="field" required value="{{ old('email') }}">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="field-label" for="reg-password">Password</label>
                <input id="reg-password" name="password" type="password" class="field" required autocomplete="new-password">
            </div>
            <div>
                <label class="field-label" for="reg-password-confirm">Confirm</label>
                <input id="reg-password-confirm" name="password_confirmation" type="password" class="field" required autocomplete="new-password">
            </div>
        </div>

        <button type="submit" class="btn-primary w-full">Create account</button>
    </form>

    <p class="mt-6 text-sm text-ink-700">
        Already have an account? <a href="{{ route('login') }}" class="font-semibold text-link hover:underline">Log in</a>
    </p>
</x-layouts.guest>

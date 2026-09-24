<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact sales — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-canvas">
<div class="mx-auto max-w-xl px-4 py-12">
    <div class="mb-6 flex items-center gap-2.5">
        <span class="flex size-9 items-center justify-center rounded-lg bg-brand-600 text-base font-black text-white">F</span>
        <span class="text-lg font-extrabold tracking-tight text-navy-900">{{ config('app.name') }}</span>
    </div>

    <div class="card p-6">
        <h1 class="text-2xl font-bold text-ink-900">Talk to our sales team</h1>
        <p class="mt-2 text-[15px] text-ink-700">
            Tell us a little about what you need and someone will be in touch within one business day.
        </p>

        @if (session('status'))
            <div class="mt-5 rounded-lg border border-brand-600/20 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <ul class="mt-5 list-inside list-disc rounded-lg border border-danger/25 bg-red-50 px-4 py-3 text-sm text-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('web-form.store') }}" class="mt-6 space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="wf-name">Your name</label>
                    <input id="wf-name" name="name" class="field" required value="{{ old('name') }}">
                </div>
                <div>
                    <label class="field-label" for="wf-email">Work email</label>
                    <input id="wf-email" name="email" type="email" class="field" required value="{{ old('email') }}">
                </div>
                <div>
                    <label class="field-label" for="wf-company">Company</label>
                    <input id="wf-company" name="company" class="field" value="{{ old('company') }}">
                </div>
                <div>
                    <label class="field-label" for="wf-phone">Phone</label>
                    <input id="wf-phone" name="phone" class="field" value="{{ old('phone') }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="field-label" for="wf-budget">Estimated budget (USD)</label>
                    <input id="wf-budget" name="budget" type="number" min="0" step="100" class="field" value="{{ old('budget') }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="field-label" for="wf-message">How can we help?</label>
                    <textarea id="wf-message" name="message" rows="4" class="field">{{ old('message') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full">Send enquiry</button>
        </form>
    </div>
</div>
</body>
</html>

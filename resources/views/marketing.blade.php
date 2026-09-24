<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — the sales CRM your pipeline deserves</title>
    <meta name="description" content="Fieldstone CRM keeps contacts, activities, deals, leads and email in one pipeline so your team always knows the next move.">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-white">
<header class="sticky top-0 z-40 border-b border-line bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-6xl items-center gap-4 px-5">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-black text-white">F</span>
            <span class="text-lg font-extrabold tracking-tight text-navy-900">Fieldstone CRM</span>
        </a>

        <nav class="ml-8 hidden items-center gap-6 text-sm font-medium text-ink-700 md:flex">
            <a href="#features" class="hover:text-ink-900">Features</a>
            <a href="#pipeline" class="hover:text-ink-900">Pipeline</a>
            <a href="#insights" class="hover:text-ink-900">Insights</a>
        </nav>

        <div class="ml-auto flex items-center gap-2">
            @auth
                <a href="{{ route('setup.index') }}" class="btn-primary">Open workspace</a>
            @else
                <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
                <a href="{{ route('register') }}" class="btn-primary">Get started free</a>
            @endauth
        </div>
    </div>
</header>

<section class="relative overflow-hidden">
    <div class="mx-auto grid max-w-6xl gap-12 px-5 py-20 lg:grid-cols-2 lg:items-center">
        <div>
            <span class="chip bg-brand-50 text-brand-600">Built for small sales teams</span>
            <h1 class="mt-5 text-[44px] leading-[1.08] font-extrabold tracking-tight text-ink-900 sm:text-[54px]">
                The sales CRM your pipeline deserves.
            </h1>
            <p class="mt-5 max-w-lg text-[17px] leading-relaxed text-ink-700">
                Fieldstone keeps contacts, activities, deals, leads and email in one place — so every rep knows the
                next move and every manager knows where revenue stands.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 text-[15px]">Start free</a>
                <a href="{{ route('login') }}" class="btn-secondary px-5 py-2.5 text-[15px]">View the demo workspace</a>
            </div>

            <dl class="mt-12 grid max-w-md grid-cols-3 gap-6">
                @foreach ([['Pipeline stages', '5'], ['Activity types', '6'], ['Setup in', '4 min']] as [$label, $value])
                    <div>
                        <dt class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">{{ $label }}</dt>
                        <dd class="mt-1 text-2xl font-bold text-ink-900">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="relative">
            <div class="overflow-hidden rounded-2xl border border-line bg-white shadow-2xl">
                <div class="flex items-center gap-1.5 border-b border-line bg-gray-50 px-4 py-2.5">
                    <span class="size-2.5 rounded-full bg-red-300"></span>
                    <span class="size-2.5 rounded-full bg-amber-300"></span>
                    <span class="size-2.5 rounded-full bg-green-300"></span>
                    <span class="ml-3 text-xs text-ink-400">fieldstone.app/deals</span>
                </div>
                <div class="flex">
                    <div class="w-32 shrink-0 space-y-1.5 bg-navy-900 p-3">
                        @foreach (['Setup guide', 'Contacts', 'Activities', 'Deals', 'Leads', 'Insights'] as $item)
                            <div class="rounded-md px-2 py-1.5 text-[11px] font-medium {{ $item === 'Deals' ? 'bg-indigo-brand text-white' : 'text-white/60' }}">
                                {{ $item }}
                            </div>
                        @endforeach
                    </div>
                    <div class="flex flex-1 gap-2 overflow-hidden p-3">
                        @foreach ([['Qualified', 3, '#c7c4f5'], ['Demo', 2, '#a7e0bd'], ['Proposal', 2, '#f6d6a8']] as [$stage, $count, $tint])
                            <div class="flex-1 space-y-2">
                                <p class="text-[10px] font-bold tracking-wide text-ink-500 uppercase">{{ $stage }}</p>
                                @for ($i = 0; $i < $count; $i++)
                                    <div class="rounded-md border border-line p-2">
                                        <div class="h-1.5 w-3/4 rounded-full" style="background-color: {{ $tint }}"></div>
                                        <div class="mt-1.5 h-1.5 w-1/2 rounded-full bg-gray-100"></div>
                                        <div class="mt-2 h-2 w-10 rounded-full bg-gray-200"></div>
                                    </div>
                                @endfor
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="absolute -top-12 -right-12 -z-10 size-64 rounded-full bg-indigo-brand/15 blur-3xl"></div>
            <div class="absolute -bottom-16 -left-10 -z-10 size-56 rounded-full bg-brand-600/15 blur-3xl"></div>
        </div>
    </div>
</section>

<section id="features" class="border-y border-line bg-canvas py-20">
    <div class="mx-auto max-w-6xl px-5">
        <h2 class="max-w-2xl text-[32px] leading-tight font-bold text-ink-900">Everything a sales team touches, in one workspace</h2>

        <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['contacts', 'Contacts', 'People and organizations with every deal, email and activity linked automatically.'],
                ['activities', 'Activities', 'Calls, meetings, tasks, deadlines, emails and lunches on a list or a weekly calendar.'],
                ['deals', 'Deals', 'A drag-and-drop pipeline with weighted values, win/lost tracking and rotting alerts.'],
                ['leads', 'Leads Inbox', 'Qualify inbound opportunities before they clutter your pipeline, then convert in one click.'],
                ['insights', 'Insights', 'Stage funnel, win rate, revenue trend and top accounts — updated as you work.'],
                ['inbox', 'Sales Inbox', 'Threaded conversations tied to the right contact and deal, with replies logged.'],
            ] as [$icon, $title, $copy])
                <article class="card p-5">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-brand">
                        <x-icon :name="$icon" class="size-5" />
                    </span>
                    <h3 class="mt-4 text-[17px] font-bold text-ink-900">{{ $title }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-ink-700">{{ $copy }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section id="pipeline" class="py-20">
    <div class="mx-auto grid max-w-6xl items-center gap-12 px-5 lg:grid-cols-2">
        <div>
            <h2 class="text-[32px] leading-tight font-bold text-ink-900">Move deals forward, not spreadsheets</h2>
            <p class="mt-4 text-[16px] leading-relaxed text-ink-700">
                Drag a card between stages and the probability, weighted value and reporting all update instantly.
                Deals without a scheduled next step are flagged before they go cold.
            </p>
            <ul class="mt-6 space-y-3">
                @foreach ([
                    'Five-stage default pipeline you can work in immediately',
                    'Won and lost tracking with structured lost reasons',
                    'Every activity, note and email attached to the deal record',
                ] as $point)
                    <li class="flex items-start gap-2.5 text-[15px] text-ink-700">
                        <x-icon name="check-circle" class="mt-0.5 size-5 shrink-0 text-brand-600" /> {{ $point }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="insights" class="card p-6">
            <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Pipeline by stage</p>
            <div class="mt-4 space-y-3">
                @foreach ([['Qualified', 70], ['Contact Made', 55], ['Demo Scheduled', 85], ['Proposal Made', 40], ['Negotiations', 25]] as [$label, $width])
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-ink-700">{{ $label }}</span>
                        </div>
                        <div class="mt-1.5 h-2.5 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-indigo-brand" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="border-t border-line bg-navy-900 py-20">
    <div class="mx-auto max-w-3xl px-5 text-center">
        <h2 class="text-[32px] leading-tight font-bold text-white">Set up your pipeline in four minutes</h2>
        <p class="mt-4 text-[16px] text-white/70">
            Create a workspace, add a contact, schedule an activity, add a deal. The setup guide walks you through it.
        </p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 text-[15px]">Get started free</a>
            <a href="{{ route('web-form.show') }}" class="btn px-5 py-2.5 text-[15px] text-white ring-1 ring-white/25 hover:bg-white/10">Talk to sales</a>
        </div>
    </div>
</section>

<footer class="border-t border-line bg-white py-8">
    <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-4 px-5 text-sm text-ink-500">
        <span class="flex items-center gap-2">
            <span class="flex size-6 items-center justify-center rounded-md bg-brand-600 text-[11px] font-black text-white">F</span>
            Fieldstone CRM
        </span>
        <span class="ml-auto">Built with Laravel {{ Illuminate\Foundation\Application::VERSION }}</span>
    </div>
</footer>
</body>
</html>

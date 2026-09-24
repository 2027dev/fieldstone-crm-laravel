<x-layouts.app title="Web Forms" breadcrumb="Leads">
    <x-slot:subnav>
        <x-leads-subnav />
    </x-slot:subnav>

    <div class="p-4 lg:p-5">
        <div class="card p-6">
            <h2 class="text-[19px] font-bold text-ink-900">Capture leads straight from your website</h2>
            <p class="mt-1.5 max-w-2xl text-[15px] text-ink-700">
                Share the hosted form or embed it in your site. Every submission creates a contact and drops a new lead
                into your Leads Inbox, ready to qualify.
            </p>

            <div class="mt-5 flex flex-wrap items-center gap-3">
                <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="btn-primary">
                    <x-icon name="external" class="size-4" /> Open hosted form
                </a>
                <code class="rounded-lg border border-line bg-gray-50 px-3 py-2 text-sm text-ink-700">{{ $publicUrl }}</code>
            </div>

            <div class="mt-6">
                <p class="field-label">Embed snippet</p>
                <pre class="overflow-x-auto rounded-lg bg-navy-950 px-4 py-3 text-[13px] leading-relaxed text-white/90"><code>&lt;iframe src="{{ $publicUrl }}" width="100%" height="720" style="border:0"&gt;&lt;/iframe&gt;</code></pre>
            </div>
        </div>

        <div class="card mt-5">
            <div class="border-b border-line px-5 py-3">
                <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Recent web form submissions</p>
            </div>
            <ul class="divide-y divide-line">
                @forelse ($submissions as $lead)
                    <li>
                        <a href="{{ route('leads.show', $lead) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                            <span class="min-w-0 flex-1 truncate text-sm font-semibold text-ink-900">{{ $lead->title }}</span>
                            <span class="shrink-0 text-xs text-ink-500">{{ $lead->created_at->diffForHumans() }}</span>
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-ink-400">No submissions yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-layouts.app>

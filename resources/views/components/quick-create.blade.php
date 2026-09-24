@php
    $qcPeople = \App\Models\Person::orderBy('name')->get(['id', 'name']);
    $qcOrganizations = \App\Models\Organization::orderBy('name')->get(['id', 'name']);
    $qcStages = \App\Models\Stage::with('pipeline')->orderBy('pipeline_id')->orderBy('position')->get();
    $qcTabs = [
        'deal' => ['label' => 'Deal', 'icon' => 'deals'],
        'person' => ['label' => 'Person', 'icon' => 'contacts'],
        'organization' => ['label' => 'Organization', 'icon' => 'building'],
        'activity' => ['label' => 'Activity', 'icon' => 'activities'],
        'lead' => ['label' => 'Lead', 'icon' => 'leads'],
    ];
@endphp

<x-modal name="quick-create" title="Create" width="max-w-xl">
    <div x-data="{ tab: 'deal' }">
        <div class="flex gap-1 border-b border-line px-4 pt-3">
            @foreach ($qcTabs as $key => $meta)
                <button type="button" x-on:click="tab = '{{ $key }}'"
                        class="flex items-center gap-1.5 rounded-t-lg px-3 py-2 text-[13px] font-semibold transition-colors"
                        x-bind:class="tab === '{{ $key }}' ? 'bg-indigo-50 text-indigo-brand' : 'text-ink-500 hover:bg-gray-50'">
                    <x-icon :name="$meta['icon']" class="size-4" /> {{ $meta['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Deal --}}
        <form x-show="tab === 'deal'" method="POST" action="{{ route('deals.store') }}" class="space-y-4 p-5">
            @csrf
            <div>
                <label class="field-label" for="qc-deal-title">Deal title</label>
                <input id="qc-deal-title" name="title" class="field" required placeholder="Acme — annual licence">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="qc-deal-value">Value</label>
                    <input id="qc-deal-value" name="value" type="number" step="0.01" min="0" class="field" placeholder="0.00">
                </div>
                <div>
                    <label class="field-label" for="qc-deal-stage">Stage</label>
                    <select id="qc-deal-stage" name="stage_id" class="field" required>
                        @foreach ($qcStages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="qc-deal-person">Contact person</label>
                    <select id="qc-deal-person" name="person_id" class="field">
                        <option value="">—</option>
                        @foreach ($qcPeople as $person)
                            <option value="{{ $person->id }}">{{ $person->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="qc-deal-org">Organization</label>
                    <select id="qc-deal-org" name="organization_id" class="field">
                        <option value="">—</option>
                        @foreach ($qcOrganizations as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="field-label" for="qc-deal-close">Expected close date</label>
                    <input id="qc-deal-close" name="expected_close_date" type="date" class="field">
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-line pt-4">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save deal</button>
            </div>
        </form>

        {{-- Person --}}
        <form x-show="tab === 'person'" x-cloak method="POST" action="{{ route('people.store') }}" class="space-y-4 p-5">
            @csrf
            <div>
                <label class="field-label" for="qc-person-name">Name</label>
                <input id="qc-person-name" name="name" class="field" required placeholder="Jane Doe">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="qc-person-org">Organization</label>
                    <select id="qc-person-org" name="organization_id" class="field">
                        <option value="">—</option>
                        @foreach ($qcOrganizations as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="qc-person-title">Job title</label>
                    <input id="qc-person-title" name="job_title" class="field">
                </div>
                <div>
                    <label class="field-label" for="qc-person-email">Email</label>
                    <input id="qc-person-email" name="email" type="email" class="field">
                </div>
                <div>
                    <label class="field-label" for="qc-person-phone">Phone</label>
                    <input id="qc-person-phone" name="phone" class="field">
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-line pt-4">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save person</button>
            </div>
        </form>

        {{-- Organization --}}
        <form x-show="tab === 'organization'" x-cloak method="POST" action="{{ route('organizations.store') }}" class="space-y-4 p-5">
            @csrf
            <div>
                <label class="field-label" for="qc-org-name">Name</label>
                <input id="qc-org-name" name="name" class="field" required placeholder="Acme Inc.">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="qc-org-industry">Industry</label>
                    <input id="qc-org-industry" name="industry" class="field">
                </div>
                <div>
                    <label class="field-label" for="qc-org-employees">Employees</label>
                    <input id="qc-org-employees" name="employee_count" type="number" min="0" class="field">
                </div>
                <div class="sm:col-span-2">
                    <label class="field-label" for="qc-org-address">Address</label>
                    <input id="qc-org-address" name="address" class="field">
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-line pt-4">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save organization</button>
            </div>
        </form>

        {{-- Activity --}}
        <form x-show="tab === 'activity'" x-cloak method="POST" action="{{ route('activities.store') }}" class="space-y-4 p-5">
            @csrf
            <div>
                <label class="field-label" for="qc-activity-subject">Subject</label>
                <input id="qc-activity-subject" name="subject" class="field" required placeholder="Discovery call">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="qc-activity-type">Type</label>
                    <select id="qc-activity-type" name="type" class="field">
                        @foreach (\App\Models\Activity::TYPES as $value => $meta)
                            <option value="{{ $value }}">{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="qc-activity-person">Contact person</label>
                    <select id="qc-activity-person" name="person_id" class="field">
                        <option value="">—</option>
                        @foreach ($qcPeople as $person)
                            <option value="{{ $person->id }}">{{ $person->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="qc-activity-date">Due date</label>
                    <input id="qc-activity-date" name="due_date" type="date" class="field" value="{{ now()->toDateString() }}">
                </div>
                <div>
                    <label class="field-label" for="qc-activity-time">Time</label>
                    <input id="qc-activity-time" name="due_time" type="time" class="field">
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-line pt-4">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save activity</button>
            </div>
        </form>

        {{-- Lead --}}
        <form x-show="tab === 'lead'" x-cloak method="POST" action="{{ route('leads.store') }}" class="space-y-4 p-5">
            @csrf
            <div>
                <label class="field-label" for="qc-lead-title">Lead title</label>
                <input id="qc-lead-title" name="title" class="field" required placeholder="Inbound demo request">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="qc-lead-value">Value</label>
                    <input id="qc-lead-value" name="value" type="number" step="0.01" min="0" class="field">
                </div>
                <div>
                    <label class="field-label" for="qc-lead-source">Source</label>
                    <input id="qc-lead-source" name="source" class="field" placeholder="Web form">
                </div>
                <div>
                    <label class="field-label" for="qc-lead-person">Contact person</label>
                    <select id="qc-lead-person" name="person_id" class="field">
                        <option value="">—</option>
                        @foreach ($qcPeople as $person)
                            <option value="{{ $person->id }}">{{ $person->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="qc-lead-org">Organization</label>
                    <select id="qc-lead-org" name="organization_id" class="field">
                        <option value="">—</option>
                        @foreach ($qcOrganizations as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-line pt-4">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save lead</button>
            </div>
        </form>
    </div>
</x-modal>

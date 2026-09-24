@props(['stages', 'people', 'organizations', 'deal' => null])

<form method="POST" action="{{ $deal ? route('deals.update', $deal) : route('deals.store') }}" class="space-y-4 p-5">
    @csrf
    @if ($deal) @method('PATCH') @endif

    <div>
        <label class="field-label" for="d-title">Deal title</label>
        <input id="d-title" name="title" class="field" required value="{{ $deal?->title }}" placeholder="Acme — annual licence">
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="field-label" for="d-value">Value</label>
            <input id="d-value" name="value" type="number" step="0.01" min="0" class="field" value="{{ $deal?->value }}" placeholder="0.00">
        </div>
        <div>
            <label class="field-label" for="d-stage">Stage</label>
            <select id="d-stage" name="stage_id" class="field" required>
                @foreach ($stages as $stage)
                    <option value="{{ $stage->id }}" @selected($deal?->stage_id === $stage->id)>{{ $stage->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="d-person">Contact person</label>
            <select id="d-person" name="person_id" class="field">
                <option value="">—</option>
                @foreach ($people as $person)
                    <option value="{{ $person->id }}" @selected($deal?->person_id === $person->id)>{{ $person->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="d-org">Organization</label>
            <select id="d-org" name="organization_id" class="field">
                <option value="">—</option>
                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected($deal?->organization_id === $organization->id)>{{ $organization->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="field-label" for="d-close">Expected close date</label>
            <input id="d-close" name="expected_close_date" type="date" class="field" value="{{ $deal?->expected_close_date?->toDateString() }}">
        </div>
        <div>
            <label class="field-label" for="d-label">Label</label>
            <input id="d-label" name="label" class="field" value="{{ $deal?->label }}" placeholder="Hot, Upsell…">
        </div>
    </div>

    <div class="flex justify-end gap-2 border-t border-line pt-4">
        <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
        <button type="submit" class="btn-primary">{{ $deal ? 'Save changes' : 'Save deal' }}</button>
    </div>
</form>

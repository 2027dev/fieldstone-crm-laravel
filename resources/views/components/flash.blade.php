@if (session('status'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
         class="mx-4 mt-4 flex items-start gap-3 rounded-lg border border-brand-600/20 bg-brand-50 px-4 py-3 lg:mx-5">
        <x-icon name="check-circle" class="mt-0.5 size-5 shrink-0 text-brand-600" />
        <p class="flex-1 text-sm font-medium text-brand-700">{{ session('status') }}</p>
        <button type="button" x-on:click="show = false" class="text-brand-700/60 hover:text-brand-700">
            <x-icon name="x" class="size-4" />
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="mx-4 mt-4 rounded-lg border border-danger/25 bg-red-50 px-4 py-3 lg:mx-5">
        <p class="text-sm font-semibold text-danger">Please fix the following:</p>
        <ul class="mt-1 list-inside list-disc text-sm text-danger/90">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

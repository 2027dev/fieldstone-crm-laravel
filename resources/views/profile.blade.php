<x-layouts.app title="Profile & settings">
    <div class="p-4 lg:p-5">
        <div class="mx-auto max-w-2xl space-y-5">
            <div class="card flex items-center gap-4 p-5">
                <x-avatar :name="$user->name" size="lg" />
                <div>
                    <h2 class="text-xl font-bold text-ink-900">{{ $user->name }}</h2>
                    <p class="text-sm text-ink-500">{{ $user->job_title ?: 'No job title' }} @if ($user->company_name) · {{ $user->company_name }} @endif</p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="card space-y-4 p-5">
                @csrf @method('PATCH')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="field-label" for="pr-name">Name</label>
                        <input id="pr-name" name="name" class="field" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div>
                        <label class="field-label" for="pr-email">Email</label>
                        <input id="pr-email" name="email" type="email" class="field" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div>
                        <label class="field-label" for="pr-company">Company</label>
                        <input id="pr-company" name="company_name" class="field" value="{{ old('company_name', $user->company_name) }}">
                    </div>
                    <div>
                        <label class="field-label" for="pr-job">Job title</label>
                        <input id="pr-job" name="job_title" class="field" value="{{ old('job_title', $user->job_title) }}">
                    </div>
                    <div>
                        <label class="field-label" for="pr-currency">Default currency</label>
                        <select id="pr-currency" name="default_currency" class="field">
                            @foreach (['USD', 'EUR', 'GBP', 'CAD', 'AUD'] as $currency)
                                <option value="{{ $currency }}" @selected($user->default_currency === $currency)>{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t border-line pt-4">
                    <p class="field-label">Change password</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <input name="password" type="password" class="field" placeholder="New password" autocomplete="new-password">
                        <input name="password_confirmation" type="password" class="field" placeholder="Confirm new password" autocomplete="new-password">
                    </div>
                    <p class="mt-1.5 text-xs text-ink-400">Leave blank to keep your current password.</p>
                </div>

                <div class="flex justify-end border-t border-line pt-4">
                    <button type="submit" class="btn-primary">Save changes</button>
                </div>
            </form>

            <div class="card p-5">
                <h3 class="text-[15px] font-bold text-ink-900">Sample data</h3>
                <p class="mt-1 text-sm text-ink-700">
                    Remove the records marked <span class="font-semibold">[Sample]</span> once you've finished exploring.
                </p>
                <form method="POST" action="{{ route('sample-data.destroy') }}" class="mt-4">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-secondary">Remove sample data</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>

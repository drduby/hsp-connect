<?php

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

new class extends Component {
    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public bool $saved = false;

    public function save(): void
    {
        $this->saved = false;

        $this->validate([
            'current_password' => ['required'],
            'new_password'     => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        if (! Hash::check($this->current_password, Auth::user()->password)) {
            $this->addError('current_password', 'The current password is incorrect.');

            return;
        }

        Auth::user()->update(['password' => Hash::make($this->new_password)]);

        ActivityLogger::log('admin.password.changed', 'Admin changed their password', Auth::id());

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->saved = true;
    }
};
?>

<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-md">

        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-900">Change Password</h1>
            <p class="mt-1 text-sm text-gray-500">Minimum 8 characters with letters and numbers.</p>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Current password</label>
                <input wire:model="current_password"
                       type="password"
                       autocomplete="current-password"
                       placeholder="Enter your current password"
                       class="w-full rounded-lg border {{ $errors->has('current_password') ? 'border-red-400' : 'border-gray-300' }} py-2.5 px-3 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('current_password')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="size-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">New password</label>
                <input wire:model="new_password"
                       type="password"
                       autocomplete="new-password"
                       placeholder="At least 8 characters"
                       class="w-full rounded-lg border {{ $errors->has('new_password') ? 'border-red-400' : 'border-gray-300' }} py-2.5 px-3 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('new_password')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="size-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm new password</label>
                <input wire:model="new_password_confirmation"
                       type="password"
                       autocomplete="new-password"
                       placeholder="Repeat new password"
                       class="w-full rounded-lg border border-gray-300 py-2.5 px-3 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="flex items-center justify-between pt-2">
                <div>
                    @if($saved)
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-green-700">
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                            </svg>
                            Password updated
                        </span>
                    @endif
                </div>
                <button wire:click="save"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-x-1.5 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-50 transition-colors whitespace-nowrap">
                    <span wire:loading.remove>Save password</span>
                    <span wire:loading>Saving…</span>
                </button>
            </div>

        </div>
    </div>
</div>

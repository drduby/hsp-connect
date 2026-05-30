<?php

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public bool $saved = false;

    public function save(): void
    {
        $this->validate([
            'current_password'      => ['required'],
            'new_password'          => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
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

<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-lg">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">Change Password</h1>
        <p class="mt-1 text-sm text-gray-500">Update your admin account password.</p>
    </div>

    @if($saved)
        <div class="rounded-md bg-green-50 p-4 mb-6">
            <div class="flex">
                <svg class="size-5 text-green-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800">Password updated successfully.</p>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
        <div class="p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current password</label>
                <input wire:model="current_password"
                       type="password"
                       autocomplete="current-password"
                       class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-sm">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New password</label>
                <input wire:model="new_password"
                       type="password"
                       autocomplete="new-password"
                       class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-sm">
                @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm new password</label>
                <input wire:model="new_password_confirmation"
                       type="password"
                       autocomplete="new-password"
                       class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-sm">
            </div>
        </div>

        <div class="flex items-center justify-end gap-x-3 border-t border-gray-900/10 px-6 py-4">
            <button wire:click="save"
                    wire:loading.attr="disabled"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50">
                <span wire:loading.remove>Save password</span>
                <span wire:loading>Saving…</span>
            </button>
        </div>
    </div>
</div>

<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $nickname = '';
    public string $email = '';
    public bool $is_admin = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->nickname = $user->nickname;
        $this->email = $user->email;
        $this->is_admin = (bool) $user->is_admin;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $this->editingId],
            'is_admin' => ['boolean'],
        ]);

        User::findOrFail($this->editingId)->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
        ]);

        $this->showModal = false;
        session()->flash('success', 'User updated.');
    }

    public function block(int $id): void
    {
        User::find($id)?->update(['blocked_at' => now()]);
        session()->flash('success', 'User blocked.');
    }

    public function unblock(int $id): void
    {
        User::find($id)?->update(['blocked_at' => null]);
        session()->flash('success', 'User unblocked.');
    }

    public function verifyEmail(int $id): void
    {
        $user = User::find($id);
        if ($user && ! $user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
            session()->flash('success', $user->nickname . ' has been manually verified.');
        }
    }

    public function sendVerificationEmail(int $id): void
    {
        $user = User::find($id);
        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            session()->flash('success', 'Verification email sent to ' . $user->email . '.');
        }
    }

    public function impersonate(int $id): void
    {
        $user = User::findOrFail($id);
        abort_if($user->is_admin, 403);
        abort_if($user->id === auth()->id(), 403);

        session(['impersonating_admin_id' => auth()->id()]);
        \Illuminate\Support\Facades\Auth::loginUsingId($user->id);
        session()->regenerate();

        $this->redirect('/', navigate: false);
    }

    public function delete(int $id): void
    {
        abort_if($id === auth()->id(), 403);
        User::find($id)?->delete();
        session()->flash('success', 'User deleted.');
    }

    #[Computed]
    public function users(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return User::when(
            $this->search,
            fn ($q) => $q->where(function ($q) {
                $q->where('nickname', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
            })
        )
            ->orderByDesc('created_at')
            ->paginate(10);
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700 ring-1 ring-green-200">{{ session('success') }}</div>
    @endif

    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-x-3">
            <h1 class="text-2xl font-bold text-gray-900">Users</h1>
            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700">
                {{ $this->users->total() }}
            </span>
        </div>
        <input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Search by name, nickname or email…"
            class="block w-72 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Nickname</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Full Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Registered</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($this->users as $user)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $user->nickname }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            @if($user->is_admin)
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-700">Admin</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">User</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            @if($user->isBlocked())
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Blocked</span>
                            @elseif(! $user->hasVerifiedEmail())
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">Unverified</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $user->created_at->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="relative inline-block text-left" x-data="{ open: false, up: false }" @click.outside="open = false">
                                <button @click="up = ($el.getBoundingClientRect().bottom + 220 > window.innerHeight); open = !open"
                                        class="inline-flex items-center justify-center rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                                    </svg>
                                </button>

                                <div x-show="open"
                                     x-transition
                                     class="absolute right-0 z-20 w-48 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200" :class="up ? 'bottom-full mb-1 origin-bottom-right' : 'top-full mt-1 origin-top-right'">

                                    <button wire:click="openEdit({{ $user->id }})" @click="open = false"
                                            class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.885L17.5 5.5a2.121 2.121 0 0 0-3-3L3.58 13.42a4 4 0 0 0-.885 1.343Z"/></svg>
                                        Edit
                                    </button>

                                    @if(! $user->is_admin && auth()->id() !== $user->id)
                                        <button wire:click="impersonate({{ $user->id }})" wire:confirm="Log in as {{ $user->nickname }}?" @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-indigo-700 hover:bg-gray-50">
                                            <svg class="h-4 w-4 text-indigo-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z"/></svg>
                                            Login as user
                                        </button>
                                    @endif

                                    @if(! $user->hasVerifiedEmail())
                                        <button wire:click="verifyEmail({{ $user->id }})" wire:confirm="Manually mark this user as verified?" @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-green-700 hover:bg-gray-50">
                                            <svg class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.403 12.652a3 3 0 0 0 0-5.304 3 3 0 0 0-3.75-3.751 3 3 0 0 0-5.305 0 3 3 0 0 0-3.751 3.75 3 3 0 0 0 0 5.305 3 3 0 0 0 3.75 3.751 3 3 0 0 0 5.305 0 3 3 0 0 0 3.751-3.75Zm-2.546-4.46a.75.75 0 0 0-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                                            Verify manually
                                        </button>
                                        <button wire:click="sendVerificationEmail({{ $user->id }})" @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-blue-700 hover:bg-gray-50">
                                            <svg class="h-4 w-4 text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/><path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/></svg>
                                            Send verification email
                                        </button>
                                    @endif

                                    @if(auth()->id() !== $user->id)
                                        <div class="my-1 border-t border-gray-100"></div>

                                        @if($user->isBlocked())
                                            <button wire:click="unblock({{ $user->id }})" @click="open = false"
                                                    class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                                                Unblock
                                            </button>
                                        @else
                                            <button wire:click="block({{ $user->id }})" @click="open = false"
                                                    class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-yellow-700 hover:bg-gray-50">
                                                <svg class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/></svg>
                                                Block
                                            </button>
                                        @endif

                                        <button wire:click="delete({{ $user->id }})" wire:confirm="Are you sure you want to delete this user?" @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="h-4 w-4 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/></svg>
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->users->hasPages())
        <div class="mt-6">
            {{ $this->users->links('admin.pagination') }}
        </div>
    @endif

    {{-- Edit Modal --}}
    <div x-show="$wire.showModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6" @click.outside="$wire.showModal = false">
            <h2 class="text-lg font-semibold text-gray-900 mb-5">Edit User</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input wire:model="first_name" type="text"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input wire:model="last_name" type="text"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        @error('last_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nickname</label>
                    <input wire:model="nickname" type="text"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @error('nickname') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input wire:model="email" type="email"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-x-2">
                    <input wire:model="is_admin" type="checkbox" id="is_admin_modal"
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_admin_modal" class="text-sm font-medium text-gray-700">Administrator</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-x-3">
                <button wire:click="$set('showModal', false)"
                        class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                <button wire:click="save"
                        class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">Save</button>
            </div>
        </div>
    </div>
</div>

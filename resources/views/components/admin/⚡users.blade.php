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
            ->paginate(20);
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
            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                {{ $this->users->total() }}
            </span>
        </div>
        <input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Search name, nickname or email…"
            class="block w-72 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200">
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
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $user->created_at->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-x-3">
                                <button wire:click="openEdit({{ $user->id }})"
                                        class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit</button>

                                @if(auth()->id() !== $user->id)
                                    @if($user->isBlocked())
                                        <button wire:click="unblock({{ $user->id }})"
                                                class="text-sm font-medium text-gray-600 hover:text-gray-800">Unblock</button>
                                    @else
                                        <button wire:click="block({{ $user->id }})"
                                                class="text-sm font-medium text-yellow-600 hover:text-yellow-800">Block</button>
                                    @endif

                                    <button wire:click="delete({{ $user->id }})"
                                            wire:confirm="Are you sure you want to delete this user?"
                                            class="text-sm font-medium text-red-600 hover:text-red-800">Delete</button>
                                @endif
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
            {{ $this->users->links() }}
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

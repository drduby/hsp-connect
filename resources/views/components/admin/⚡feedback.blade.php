<?php

use App\Models\Feedback;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $type = '';
    public ?int $viewingId = null;

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function openDetail(int $id): void
    {
        $this->viewingId = $id;
    }

    public function delete(int $id): void
    {
        Feedback::find($id)?->delete();
        if ($this->viewingId === $id) {
            $this->viewingId = null;
        }
        session()->flash('success', 'Feedback deleted.');
    }

    #[Computed]
    public function ideaCount(): int
    {
        return Feedback::where('type', 'idea')->count();
    }

    #[Computed]
    public function bugCount(): int
    {
        return Feedback::where('type', 'bug')->count();
    }

    #[Computed]
    public function feedbacks(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Feedback::with('user')
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    #[Computed]
    public function viewing(): ?Feedback
    {
        return $this->viewingId ? Feedback::with('user')->find($this->viewingId) : null;
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700 ring-1 ring-green-200">{{ session('success') }}</div>
    @endif

    <div class="mb-8 flex items-center gap-x-3">
        <h1 class="text-2xl font-bold text-gray-900">Feedback</h1>
        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-800">
            💡 {{ $this->ideaCount }}
        </span>
        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
            🐛 {{ $this->bugCount }}
        </span>
    </div>

    {{-- Type filter tabs --}}
    <div class="mb-6 flex gap-x-1 border-b border-gray-200">
        @foreach(['' => 'All', 'idea' => '💡 Ideas & Wishes', 'bug' => '🐛 Technical Issues'] as $value => $label)
            <button wire:click="$set('type', '{{ $value }}')"
                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors
                           {{ $type === $value ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">From</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Topic</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($this->feedbacks as $fb)
                    <tr wire:key="{{ $fb->id }}" class="hover:bg-gray-50/60 cursor-pointer" wire:click="openDetail({{ $fb->id }})">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($fb->type === 'idea')
                                <span class="inline-flex items-center gap-x-1 rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-800">💡 Idea</span>
                            @else
                                <span class="inline-flex items-center gap-x-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">🐛 Bug</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                            {{ $fb->user?->nickname ?? ($fb->email ?? 'Anonymous') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                            {{ $fb->topic ? Str::limit($fb->topic, 30) : '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                            <span class="block truncate">{{ Str::limit($fb->description, 60) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $fb->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap" wire:click.stop>
                            <div class="relative inline-block text-left" x-data="{ open: false, up: false }" @click.outside="open = false">
                                <button @click="up = ($el.getBoundingClientRect().bottom + 120 > window.innerHeight); open = !open"
                                        class="inline-flex items-center justify-center rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute right-0 z-20 w-40 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200"
                                     :class="up ? 'bottom-full mb-1 origin-bottom-right' : 'top-full mt-1 origin-top-right'">
                                    <button wire:click="openDetail({{ $fb->id }})" @click="open = false"
                                            class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/></svg>
                                        View
                                    </button>
                                    <div class="my-1 border-t border-gray-100"></div>
                                    <button wire:click="delete({{ $fb->id }})" wire:confirm="Delete this feedback?" @click="open = false"
                                            class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="h-4 w-4 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/></svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No feedback yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->feedbacks->hasPages())
        <div class="mt-6">
            {{ $this->feedbacks->links('admin.pagination') }}
        </div>
    @endif

    {{-- Detail modal --}}
    <div x-show="$wire.viewingId !== null"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6" @click.outside="$wire.viewingId = null">
            @if($this->viewing)
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-x-2">
                        @if($this->viewing->type === 'idea')
                            <span class="inline-flex items-center gap-x-1 rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800">💡 Idea / Wish</span>
                        @else
                            <span class="inline-flex items-center gap-x-1 rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-700">🐛 Technical Issue</span>
                        @endif
                    </div>
                    <button wire:click="$set('viewingId', null)" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                    </button>
                </div>

                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">From</dt>
                        <dd class="mt-1 text-gray-900">
                            {{ $this->viewing->user?->nickname ?? '—' }}
                            @if($this->viewing->email)
                                <span class="text-gray-400">({{ $this->viewing->email }})</span>
                            @elseif($this->viewing->user?->email)
                                <span class="text-gray-400">({{ $this->viewing->user->email }})</span>
                            @endif
                        </dd>
                    </div>
                    @if($this->viewing->topic)
                        <div>
                            <dt class="font-medium text-gray-500">Topic</dt>
                            <dd class="mt-1 text-gray-900">{{ $this->viewing->topic }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-gray-900 leading-relaxed whitespace-pre-wrap">{{ $this->viewing->description }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Submitted</dt>
                        <dd class="mt-1 text-gray-900">{{ $this->viewing->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex justify-end gap-x-3">
                    <button wire:click="delete({{ $this->viewing->id }})" wire:confirm="Delete this feedback?"
                            class="rounded-md bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-600 hover:bg-red-100">Delete</button>
                    <button wire:click="$set('viewingId', null)"
                            class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Close</button>
                </div>
            @endif
        </div>
    </div>
</div>

<?php

use App\Models\PostReport;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $status = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function review(int $id): void
    {
        PostReport::find($id)?->update(['status' => 'reviewed']);
        session()->flash('success', 'Marked as reviewed.');
    }

    public function dismiss(int $id): void
    {
        PostReport::find($id)?->update(['status' => 'dismissed']);
        session()->flash('success', 'Dismissed.');
    }

    public function deletePost(int $id): void
    {
        $report = PostReport::find($id);
        if ($report) {
            $report->post?->delete();
            $report->update(['status' => 'reviewed']);
        }
        session()->flash('success', 'Post deleted.');
    }

    #[Computed]
    public function pendingCount(): int
    {
        return PostReport::where('status', 'pending')->count();
    }

    #[Computed]
    public function reports(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return PostReport::with(['post.user', 'user'])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByRaw("FIELD(status,'pending','reviewed','dismissed')")
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
            <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
            @if($this->pendingCount > 0)
                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">
                    {{ $this->pendingCount }} pending
                </span>
            @endif
        </div>
    </div>

    {{-- Status filter tabs --}}
    <div class="mb-6 flex gap-x-1 border-b border-gray-200">
        <button wire:click="$set('status', '')"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors
                       {{ $status === '' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            All
        </button>
        <button wire:click="$set('status', 'pending')"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors
                       {{ $status === 'pending' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Pending
        </button>
        <button wire:click="$set('status', 'reviewed')"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors
                       {{ $status === 'reviewed' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Reviewed
        </button>
        <button wire:click="$set('status', 'dismissed')"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors
                       {{ $status === 'dismissed' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Dismissed
        </button>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Reporter</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Post</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Reason</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($this->reports as $report)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            {{ $report->user?->nickname ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs">
                            @if($report->post)
                                <span class="block truncate">
                                    {{ Str::limit($report->post->title ?? $report->post->body, 50) }}
                                </span>
                                <span class="text-xs text-gray-400">by {{ $report->post->user?->nickname ?? '—' }}</span>
                            @else
                                <span class="italic text-gray-400">Deleted</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $report->reason }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                            <span class="block truncate">{{ Str::limit($report->description, 60) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            @if($report->status === 'pending')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">Pending</span>
                            @elseif($report->status === 'reviewed')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Reviewed</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Dismissed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $report->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @if($report->status === 'pending')
                                <div class="relative inline-block text-left" x-data="{ open: false, up: false }" @click.outside="open = false">
                                    <button @click="up = ($el.getBoundingClientRect().bottom + 150 > window.innerHeight); open = !open"
                                            class="inline-flex items-center justify-center rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition
                                         class="absolute right-0 z-20 w-44 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200" :class="up ? 'bottom-full mb-1 origin-bottom-right' : 'top-full mt-1 origin-top-right'">
                                        <button wire:click="review({{ $report->id }})" @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-indigo-700 hover:bg-gray-50">
                                            <svg class="h-4 w-4 text-indigo-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.403 12.652a3 3 0 0 0 0-5.304 3 3 0 0 0-3.75-3.751 3 3 0 0 0-5.305 0 3 3 0 0 0-3.751 3.75 3 3 0 0 0 0 5.305 3 3 0 0 0 3.75 3.751 3 3 0 0 0 5.305 0 3 3 0 0 0 3.751-3.75Zm-2.546-4.46a.75.75 0 0 0-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                                            Mark reviewed
                                        </button>
                                        <button wire:click="dismiss({{ $report->id }})" @click="open = false"
                                                class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/></svg>
                                            Dismiss
                                        </button>
                                        @if($report->post)
                                            <div class="my-1 border-t border-gray-100"></div>
                                            <button wire:click="deletePost({{ $report->id }})" wire:confirm="Are you sure you want to delete this post?" @click="open = false"
                                                    class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="h-4 w-4 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/></svg>
                                                Delete post
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">No reports found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->reports->hasPages())
        <div class="mt-6">
            {{ $this->reports->links('admin.pagination') }}
        </div>
    @endif
</div>

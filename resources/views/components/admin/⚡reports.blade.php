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

    <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200">
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
                            <div class="flex items-center justify-end gap-x-3">
                                @if($report->status === 'pending')
                                    <button wire:click="review({{ $report->id }})"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Review</button>
                                    <button wire:click="dismiss({{ $report->id }})"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-800">Dismiss</button>
                                    @if($report->post)
                                        <button wire:click="deletePost({{ $report->id }})"
                                                wire:confirm="Are you sure you want to delete this post?"
                                                class="text-sm font-medium text-red-600 hover:text-red-800">Delete Post</button>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </div>
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

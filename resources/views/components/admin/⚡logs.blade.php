<?php

use App\Models\ActivityLog;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $tab = 'activity';

    public string $search = '';

    public string $filterAction = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAction(): void
    {
        $this->resetPage();
    }

    public function switchTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    #[Computed]
    public function activityLogs(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return ActivityLog::with('user')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('description', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', fn ($q) => $q->where('nickname', 'like', '%'.$this->search.'%'));
            }))
            ->when($this->filterAction, fn ($q) => $q->where('action', $this->filterAction))
            ->orderByDesc('created_at')
            ->paginate(30);
    }

    #[Computed]
    public function actionTypes(): array
    {
        return ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->toArray();
    }

    #[Computed]
    public function systemLogLines(): array
    {
        $path = storage_path('logs/laravel.log');
        if (! file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);
        if ($content === false) {
            return [];
        }

        preg_match_all(
            '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \S+\.(\w+): (.*?)(?=\[\d{4}-\d{2}-\d{2}|\z)/s',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        $lines = array_map(fn ($m) => [
            'timestamp' => $m[1],
            'level' => strtoupper($m[2]),
            'message' => trim(preg_replace('/\s+/', ' ', mb_substr($m[3], 0, 300))),
        ], $matches);

        return array_slice(array_reverse($lines), 0, 200);
    }
};
?>

<div>
    {{-- Tabs --}}
    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Logs</h1>
        <div class="flex items-center gap-x-1 rounded-lg bg-gray-100 p-1">
            <button wire:click="switchTab('activity')"
                class="rounded-md px-4 py-1.5 text-sm font-medium transition {{ $tab === 'activity' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                Activity Log
            </button>
            <button wire:click="switchTab('system')"
                class="rounded-md px-4 py-1.5 text-sm font-medium transition {{ $tab === 'system' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                System Log
            </button>
        </div>
    </div>

    {{-- Activity Log Tab --}}
    @if($tab === 'activity')
        <div class="mb-6 flex flex-wrap items-center gap-3">
            <input
                wire:model.live.debounce.200ms="search"
                type="search"
                placeholder="Search by user or description…"
                class="block w-72 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
            <select wire:model.live="filterAction"
                class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">All actions</option>
                @foreach($this->actionTypes as $action)
                    <option value="{{ $action }}">{{ $action }}</option>
                @endforeach
            </select>
            <span class="text-sm text-gray-500">{{ number_format($this->activityLogs->total()) }} entries</span>
        </div>

        <div class="overflow-x-auto rounded-xl bg-white shadow ring-1 ring-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-40 px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Time</th>
                        <th class="w-36 px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Description</th>
                        <th class="w-32 px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">User</th>
                        <th class="w-32 px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($this->activityLogs as $log)
                        @php
                            $badge = match(true) {
                                str_starts_with($log->action, 'login') => 'bg-blue-100 text-blue-700',
                                str_starts_with($log->action, 'admin') => 'bg-purple-100 text-purple-700',
                                str_contains($log->action, 'deleted') || str_contains($log->action, 'blocked') => 'bg-red-100 text-red-700',
                                str_contains($log->action, 'created') || str_contains($log->action, 'unblocked') => 'bg-green-100 text-green-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <tr wire:key="log-{{ $log->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 max-w-sm truncate" title="{{ $log->description }}">{{ $log->description }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $log->user?->nickname ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs font-mono text-gray-400">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">No activity logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->activityLogs->hasPages())
            <div class="mt-6">
                {{ $this->activityLogs->links('admin.pagination') }}
            </div>
        @endif
    @endif

    {{-- System Log Tab --}}
    @if($tab === 'system')
        @php $lines = $this->systemLogLines; @endphp

        <div class="mb-4 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Last {{ count($lines) }} entries from
                <code class="rounded bg-gray-100 px-1 text-xs">storage/logs/laravel.log</code>
            </p>
            @if(count($lines))
                @php $counts = array_count_values(array_column($lines, 'level')); @endphp
                <div class="flex gap-x-2 text-xs">
                    @foreach(['ERROR' => 'red', 'WARNING' => 'yellow', 'INFO' => 'blue', 'DEBUG' => 'gray'] as $lvl => $color)
                        @if(isset($counts[$lvl]))
                            <span class="rounded-full bg-{{ $color }}-100 px-2 py-0.5 font-medium text-{{ $color }}-700">
                                {{ $lvl }}: {{ $counts[$lvl] }}
                            </span>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="overflow-x-auto rounded-xl bg-gray-950 shadow">
            <table class="min-w-full divide-y divide-gray-800">
                <thead>
                    <tr>
                        <th class="w-40 px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Time</th>
                        <th class="w-24 px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Level</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($lines as $i => $line)
                        @php
                            $levelColor = match($line['level']) {
                                'ERROR', 'CRITICAL', 'EMERGENCY', 'ALERT' => 'text-red-400',
                                'WARNING' => 'text-yellow-400',
                                'INFO' => 'text-blue-400',
                                'DEBUG' => 'text-gray-500',
                                default => 'text-gray-400',
                            };
                        @endphp
                        <tr wire:key="syslog-{{ $i }}" class="hover:bg-gray-900">
                            <td class="px-4 py-2 text-xs text-gray-500 whitespace-nowrap font-mono">{{ $line['timestamp'] }}</td>
                            <td class="px-4 py-2 text-xs font-bold {{ $levelColor }}">{{ $line['level'] }}</td>
                            <td class="px-4 py-2 text-xs text-gray-300 font-mono max-w-2xl break-all">{{ $line['message'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-sm text-gray-500">No log entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

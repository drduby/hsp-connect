<?php

use App\Services\ActivityLogger;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $editKey = '';

    public string $editDe = '';

    public string $editEn = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function edit(string $key): void
    {
        $de = require base_path('lang/de/ui.php');
        $en = require base_path('lang/en/ui.php');

        $this->editKey = $key;
        $this->editDe = (string) Arr::get($de, $key, '');
        $this->editEn = (string) Arr::get($en, $key, '');
    }

    public function save(): void
    {
        $de = require base_path('lang/de/ui.php');
        $en = require base_path('lang/en/ui.php');

        Arr::set($de, $this->editKey, $this->editDe);
        Arr::set($en, $this->editKey, $this->editEn);

        file_put_contents(base_path('lang/de/ui.php'), $this->toPhpFile($de));
        file_put_contents(base_path('lang/en/ui.php'), $this->toPhpFile($en));

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(base_path('lang/de/ui.php'), true);
            opcache_invalidate(base_path('lang/en/ui.php'), true);
        }

        ActivityLogger::log('admin.translation.saved', 'Translation key updated: '.$this->editKey);

        $this->editKey = '';
        session()->flash('success', 'Translation saved.');
    }

    public function cancel(): void
    {
        $this->editKey = '';
    }

    #[Computed]
    public function translations(): LengthAwarePaginator
    {
        $de = require base_path('lang/de/ui.php');
        $en = require base_path('lang/en/ui.php');

        $all = collect(Arr::dot($de))
            ->map(fn ($v, $k) => ['key' => $k, 'de' => (string) $v, 'en' => (string) Arr::get($en, $k, '')])
            ->when($this->search, fn ($c) => $c->filter(fn ($t) => str_contains(strtolower($t['key']), strtolower($this->search))
                || str_contains(strtolower($t['de']), strtolower($this->search))
                || str_contains(strtolower($t['en']), strtolower($this->search))))
            ->values();

        $perPage = 20;
        $page = $this->getPage();

        return new LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }

    private function toPhpFile(array $array): string
    {
        return "<?php\n\nreturn ".$this->formatArray($array, 1).";\n";
    }

    private function formatArray(array $array, int $depth): string
    {
        $pad = str_repeat('    ', $depth);
        $close = str_repeat('    ', $depth - 1);
        $lines = [];

        foreach ($array as $key => $value) {
            $k = "'".addcslashes((string) $key, "'\\")."'";
            if (is_array($value)) {
                $lines[] = $pad.$k.' => '.$this->formatArray($value, $depth + 1).',';
            } else {
                $v = "'".addcslashes((string) $value, "'\\")."'";
                $lines[] = $pad.$k.' => '.$v.',';
            }
        }

        return "[\n".implode("\n", $lines)."\n".$close.']';
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700 ring-1 ring-green-200">{{ session('success') }}</div>
    @endif

    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-x-3">
            <h1 class="text-2xl font-bold text-gray-900">Translations</h1>
            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                {{ $this->translations->total() }}
            </span>
        </div>
        <input
            wire:model.live.debounce.200ms="search"
            type="search"
            placeholder="Search by key or text…"
            class="block w-72 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm placeholder:text-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
    </div>

    {{-- Edit modal --}}
    @if($editKey)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="cancel">
            <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Key</p>
                        <p class="mt-0.5 font-mono text-sm font-semibold text-gray-800">{{ $editKey }}</p>
                    </div>
                    <button wire:click="cancel" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">🇩🇪 German</label>
                        <textarea wire:model="editDe" rows="5"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">🇬🇧 English</label>
                        <textarea wire:model="editEn" rows="5"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-x-3">
                    <button wire:click="cancel"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                        <span wire:loading.remove>Save</span>
                        <span wire:loading>Saving…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="overflow-x-auto rounded-xl bg-white shadow ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-56 px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Key</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">🇩🇪 German</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">🇬🇧 English</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($this->translations as $t)
                    <tr wire:key="{{ $t['key'] }}" class="{{ $editKey === $t['key'] ? 'bg-indigo-50' : 'hover:bg-gray-50' }}">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-gray-400">{{ $t['key'] }}</span>
                        </td>
                        <td class="max-w-xs px-4 py-3 text-sm text-gray-700">
                            <span class="block truncate" title="{{ $t['de'] }}">{{ $t['de'] ?: '—' }}</span>
                        </td>
                        <td class="max-w-xs px-4 py-3 text-sm text-gray-700">
                            <span class="block truncate" title="{{ $t['en'] }}">{{ $t['en'] ?: '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit('{{ $t['key'] }}')"
                                class="inline-flex items-center gap-x-1 rounded-md px-2.5 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z"/><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z"/></svg>
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500">No translations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->translations->hasPages())
        <div class="mt-6">
            {{ $this->translations->links('admin.pagination') }}
        </div>
    @endif
</div>

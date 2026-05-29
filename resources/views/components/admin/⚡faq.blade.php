<?php

use App\Models\FaqItem;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $question = '';
    public string $question_en = '';
    public string $answer = '';
    public string $answer_en = '';
    public int $sort_order = 0;
    public bool $is_published = true;

    public function openCreate(): void
    {
        $this->reset(['editingId', 'question', 'question_en', 'answer', 'answer_en', 'sort_order', 'is_published']);
        $this->is_published = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $item = FaqItem::findOrFail($id);
        $this->editingId = $item->id;
        $this->question = $item->question;
        $this->question_en = $item->question_en ?? '';
        $this->answer = $item->answer;
        $this->answer_en = $item->answer_en ?? '';
        $this->sort_order = $item->sort_order;
        $this->is_published = (bool) $item->is_published;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'question' => ['required', 'string'],
            'question_en' => ['nullable', 'string'],
            'answer' => ['required', 'string'],
            'answer_en' => ['nullable', 'string'],
            'sort_order' => ['integer', 'min:0'],
            'is_published' => ['boolean'],
        ]);

        $data = [
            'question' => $this->question,
            'question_en' => $this->question_en ?: null,
            'answer' => $this->answer,
            'answer_en' => $this->answer_en ?: null,
            'sort_order' => $this->sort_order,
            'is_published' => $this->is_published,
        ];

        if ($this->editingId) {
            FaqItem::findOrFail($this->editingId)->update($data);
            $message = 'FAQ item updated.';
        } else {
            FaqItem::create($data);
            $message = 'FAQ item created.';
        }

        $this->showModal = false;
        session()->flash('success', $message);
    }

    public function delete(int $id): void
    {
        FaqItem::find($id)?->delete();
        session()->flash('success', 'FAQ item deleted.');
    }

    #[Computed]
    public function items(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return FaqItem::orderBy('sort_order')->paginate(10);
    }
};
?>

<div>
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700 ring-1 ring-green-200">{{ session('success') }}</div>
    @endif

    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">FAQ</h1>
        <button wire:click="openCreate"
                class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">+ New Item</button>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Question (DE)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Has EN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Published</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($this->items as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $item->sort_order }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($item->question, 70) }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            @if($item->question_en)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Yes</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            @if($item->is_published)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Published</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="relative inline-block text-left" x-data="{ open: false, up: false }" @click.outside="open = false">
                                <button @click="up = ($el.getBoundingClientRect().bottom + 120 > window.innerHeight); open = !open"
                                        class="inline-flex items-center justify-center rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute right-0 z-20 w-40 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200" :class="up ? 'bottom-full mb-1 origin-bottom-right' : 'top-full mt-1 origin-top-right'">
                                    <button wire:click="openEdit({{ $item->id }})" @click="open = false"
                                            class="flex w-full items-center gap-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.885L17.5 5.5a2.121 2.121 0 0 0-3-3L3.58 13.42a4 4 0 0 0-.885 1.343Z"/></svg>
                                        Edit
                                    </button>
                                    <div class="my-1 border-t border-gray-100"></div>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Are you sure you want to delete this FAQ item?" @click="open = false"
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
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No FAQ items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->items->hasPages())
        <div class="mt-6">
            {{ $this->items->links('admin.pagination') }}
        </div>
    @endif

    {{-- Create / Edit Modal --}}
    <div x-show="$wire.showModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-gray-900 mb-5">
                {{ $editingId ? 'Edit FAQ Item' : 'New FAQ Item' }}
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question (DE)</label>
                    <textarea wire:model="question" rows="3"
                              class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                    @error('question') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question (EN)</label>
                    <textarea wire:model="question_en" rows="3"
                              class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                    @error('question_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Answer (DE)</label>
                    <textarea wire:model="answer" rows="5"
                              class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                    @error('answer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Answer (EN)</label>
                    <textarea wire:model="answer_en" rows="5"
                              class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                    @error('answer_en') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input wire:model="sort_order" type="number" min="0"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @error('sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-x-2">
                    <input wire:model="is_published" type="checkbox" id="faq_is_published"
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="faq_is_published" class="text-sm font-medium text-gray-700">Published</label>
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

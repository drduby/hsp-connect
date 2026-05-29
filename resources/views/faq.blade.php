@extends('layouts.app')

@section('title', __('ui.faq.title'))
@section('description', __('ui.faq.description'))
@section('canonical', route('faq'))

@section('structured-data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    @foreach($faqItems as $item)
    {
      "@@type": "Question",
      "name": {{ Js::from($item->localizedQuestion) }},
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": {{ Js::from($item->localizedAnswer) }}
      }
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endsection

@section('content')

    <x-header />

    <x-hero :member-count="$memberCount" :post-count="$postCount" :online-count="$onlineCount" />

    <div class="layout" style="position:relative;z-index:1;grid-template-columns:1fr">
        <main style="min-width:0;width:100%"
            x-data="{
                search: '',
                activeTags: [],
                items: {{ Js::from($faqItems->map(fn ($f) => ['q' => $f->localizedQuestion, 'a' => $f->localizedAnswer, 'tags' => $f->tags ?? []])->values()) }},
                get filtered() {
                    return this.items.filter(item => {
                        const q = this.search.toLowerCase();
                        const matchSearch = !q || item.q.toLowerCase().includes(q) || item.a.toLowerCase().includes(q);
                        const matchTags = this.activeTags.length === 0 || (item.tags && item.tags.some(t => this.activeTags.includes(t)));
                        return matchSearch && matchTags;
                    });
                },
                toggleTag(tag) {
                    const i = this.activeTags.indexOf(tag);
                    i === -1 ? this.activeTags.push(tag) : this.activeTags.splice(i, 1);
                }
            }">

            {{-- Search --}}
            <div class="compose" style="margin-bottom:14px">
                <div class="c-srch-row">
                    <div class="c-srch-wrap">
                        <input type="text" name="fake-user" style="display:none" tabindex="-1" aria-hidden="true">
                        <input type="password" name="fake-pass" style="display:none" tabindex="-1" aria-hidden="true">
                        <svg class="c-srch-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input x-model="search"
                            type="text"
                            placeholder="{{ __('ui.faq.search_placeholder') }}"
                            autocomplete="off"
                            class="c-srch-inp">
                        <button x-show="search" x-on:click="search = ''" class="c-srch-x">&#xD7;</button>
                    </div>
                </div>

                {{-- Tag chips --}}
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px">
                    @foreach($tags as $tag)
                        <button
                            x-on:click="toggleTag('{{ strtolower($tag->name) }}')"
                            :class="activeTags.includes('{{ strtolower($tag->name) }}') ? 'faq-chip faq-chip-on' : 'faq-chip'">
                            # {{ $tag->localizedName }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- FAQ accordion --}}
            <template x-for="(item, i) in filtered" :key="i">
                <div x-data="{ open: false }"
                    style="background:var(--surf);border:1px solid rgba(10,110,122,.1);border-radius:14px;margin-bottom:10px;overflow:hidden;box-shadow:var(--sh)">
                    <button x-on:click="open = !open"
                        style="width:100%;padding:16px 20px;background:none;border:none;display:flex;align-items:center;justify-content:space-between;gap:14px;cursor:pointer;text-align:left;font-family:var(--body)">
                        <span x-text="item.q" style="font-size:14.5px;font-weight:700;color:var(--ink);line-height:1.4"></span>
                        <span x-text="open ? '−' : '+'" style="font-size:18px;color:var(--t);flex-shrink:0"></span>
                    </button>
                    <div x-show="open" x-transition
                        style="padding:0 20px 16px;font-size:13.5px;color:var(--ink2);line-height:1.8;font-weight:300;border-top:1px solid rgba(10,110,122,.08)"
                        x-text="item.a">
                    </div>
                </div>
            </template>

            <div x-show="filtered.length === 0"
                style="text-align:center;padding:40px;color:var(--muted);font-size:14px">
                {{ __('ui.faq.no_results') }}
            </div>
        </main>
    </div>

    <x-footer />

@endsection

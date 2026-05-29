@props([
    'tags' => [],
])

<aside class="lsb">
    <div class="sb-card" id="auth-nav" style="display:none">
        <div class="sb-hdg">{{ __('ui.sidebar.my_area') }}</div>
        <button class="fb-btn" id="nav-saved" onclick="showSaved()" style="margin-bottom:6px">&#x1F516; {{ __('ui.sidebar.saved') }}<span id="nav-saved-count"></span>
        </button>
        <button class="fb-btn" id="nav-mine" onclick="showMine()">&#x270F;&#xFE0F; {{ __('ui.sidebar.my_posts') }}<span id="nav-mine-count"></span></button>
    </div>
    <div class="sb-card">
        <div class="sb-hdg">{{ __('ui.sidebar.topics') }}</div>
        <div class="tlist" id="sb-tags">
            @foreach($tags as $tag)
                <button class="titem" id="nav-{{ $tag->name }}" data-tag="{{ $tag->name }}" data-label="{{ $tag->localizedName }}">
                    <span class="tlbl">
                        <span class="tdot" style="background:{{ $tag->color ?? 'var(--t)' }}"></span>
                        # {{ $tag->localizedName }}
                    </span>
                    <span class="tcnt">{{ $tag->posts_count }}</span>
                </button>
            @endforeach
        </div>
    </div>
    <div class="sb-card" id="fb-widget">
        <div class="sb-hdg">{{ __('ui.sidebar.feedback') }}</div>
        <button class="fb-btn" onclick="openFeedback('idee')" style="margin-bottom:6px">&#x1F4A1; {{ __('ui.sidebar.idea') }}</button>
        <button class="fb-btn fb-btn-red" onclick="openFeedback('problem')">&#x1F41B; {{ __('ui.sidebar.bug') }}</button>
    </div>
</aside>

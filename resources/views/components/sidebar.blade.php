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

{{-- Mobile FAB + bottom drawer --}}
<button class="mob-fab" id="mob-fab" onclick="openMobDrawer()">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="14" y2="12"/><line x1="4" y1="18" x2="10" y2="18"/>
    </svg>
    {{ __('ui.sidebar.topics') }}
</button>

<div class="mob-drawer-overlay" id="mob-drawer-overlay" onclick="closeMobDrawer()"></div>

<div class="mob-drawer" id="mob-drawer">
    <div class="mob-drawer-handle"></div>
    <div class="mob-drawer-inner">

        {{-- My Area (shown via JS when logged in) --}}
        <div id="mob-auth-nav" style="display:none">
            <div class="sb-hdg">{{ __('ui.sidebar.my_area') }}</div>
            <div style="display:flex;gap:8px;margin-bottom:16px">
                <button class="fb-btn" id="mob-nav-saved" onclick="showSaved();closeMobDrawer()" style="flex:1">
                    &#x1F516; {{ __('ui.sidebar.saved') }}
                </button>
                <button class="fb-btn" id="mob-nav-mine" onclick="showMine();closeMobDrawer()" style="flex:1">
                    &#x270F;&#xFE0F; {{ __('ui.sidebar.my_posts') }}
                </button>
            </div>
        </div>

        {{-- Topics --}}
        <div class="sb-hdg">{{ __('ui.sidebar.topics') }}</div>
        <div style="display:flex;flex-direction:column;gap:1px;margin-bottom:16px" id="mob-tags">
            @foreach($tags as $tag)
                <button class="titem" id="mob-nav-{{ $tag->name }}" data-tag="{{ $tag->name }}" data-label="{{ $tag->localizedName }}"
                    onclick="closeMobDrawer()">
                    <span class="tlbl">
                        <span class="tdot" style="background:{{ $tag->color ?? 'var(--t)' }}"></span>
                        # {{ $tag->localizedName }}
                    </span>
                    <span class="tcnt">{{ $tag->posts_count }}</span>
                </button>
            @endforeach
        </div>

        {{-- Feedback --}}
        <div class="sb-hdg">{{ __('ui.sidebar.feedback') }}</div>
        <div style="display:flex;gap:8px">
            <button class="fb-btn" onclick="openFeedback('idee');closeMobDrawer()" style="flex:1">&#x1F4A1; {{ __('ui.sidebar.idea') }}</button>
            <button class="fb-btn fb-btn-red" onclick="openFeedback('problem');closeMobDrawer()" style="flex:1">&#x1F41B; {{ __('ui.sidebar.bug') }}</button>
        </div>

    </div>
</div>

<script>
function openMobDrawer() {
    document.getElementById('mob-drawer').classList.add('open');
    document.getElementById('mob-drawer-overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeMobDrawer() {
    document.getElementById('mob-drawer').classList.remove('open');
    document.getElementById('mob-drawer-overlay').classList.remove('open');
    document.body.style.overflow = '';
}
// Sync active tag state into drawer buttons
document.addEventListener('mob-tag-sync', function(e) {
    document.querySelectorAll('#mob-tags .titem').forEach(function(btn) {
        btn.classList.toggle('on', btn.dataset.tag === e.detail);
    });
});
// Show My Area in drawer when logged in
document.addEventListener('app:logged-in', function() {
    const el = document.getElementById('mob-auth-nav');
    if (el) el.style.display = 'block';
});
</script>

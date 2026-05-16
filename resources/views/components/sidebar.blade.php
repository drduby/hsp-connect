@props([
    'tags' => [],
])

<aside class="lsb">
    <div class="sb-card" id="auth-nav" style="display:none">
        <div class="sb-hdg">Mein Bereich</div>
        <button class="fb-btn" id="nav-saved" onclick="showSaved()" style="margin-bottom:6px">&#x1F516; Gespeichert
        </button>
        <button class="fb-btn" id="nav-mine" onclick="showMine()">&#x270F;&#xFE0F; Meine Beitr&#xE4;ge</button>
    </div>
    <div class="sb-card">
        <div class="sb-hdg">Themen</div>
        <div class="tlist" id="sb-tags">
            @foreach($tags as $tag)
                <button class="titem" id="nav-{{ $tag->name }}" data-tag="{{ $tag->name }}">
                    <span class="tlbl">
                        <span class="tdot" style="background:{{ $tag->color ?? 'var(--t)' }}"></span>
                        # {{ $tag->name }}
                    </span>
                    <span class="tcnt">0</span>
                </button>
            @endforeach
        </div>
    </div>
    <div class="sb-card" id="fb-widget">
        <div class="sb-hdg">Feedback</div>
        <button class="fb-btn" onclick="openFeedback('idee')" style="margin-bottom:6px">&#x1F4A1; Idee oder Wunsch
        </button>
        <button class="fb-btn fb-btn-red" onclick="openFeedback('problem')">&#x1F41B; Technisches Problem</button>
    </div>
</aside>

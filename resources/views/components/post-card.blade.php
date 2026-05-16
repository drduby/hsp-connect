@props(['post'])

@php
    $tc = $post['color'] ?? 'var(--t)';
    $avg = ($post['rCnt'] ?? 0) > 0
        ? number_format(($post['rSum'] ?? 0) / $post['rCnt'], 1)
        : '—';
    $liked  = $post['liked']  ?? false;
    $saved  = $post['saved']  ?? false;
    $mine   = $post['mine']   ?? false;
    $uRat   = $post['uRat']  ?? 0;
    $id     = $post['id'];
@endphp

<div class="post"
    id="post-{{ $id }}"
    data-type="{{ $post['type'] }}"
    data-tag="{{ $post['tag'] }}"
    data-search="{{ strtolower($post['title'].' '.$post['content'].' '.$post['tag']) }}"
    style="border-left:3px solid {{ $tc }}">

    {{-- ── Post header & body ── --}}
    <div class="post-inner">
        <div class="pmeta">
            <div class="pava">{{ $post['ava'] }}</div>
            <div>
                <div class="pname">{{ $post['author'] }}</div>
                <div class="ptime">{{ $post['time'] ?? 'vor 1h' }}</div>
            </div>
            <span class="pbadge {{ $post['type'] === 'Erfahrung' ? 'pb-e' : 'pb-f' }}">
                {{ $post['type'] }}
            </span>
            <span class="ptag"
                style="border-color:{{ $tc }}30;color:{{ $tc }}"
                onclick="toggleTag('{{ $post['tag'] }}')">
                # {{ $post['tag'] }}
            </span>
        </div>

        <div class="ptitle">{{ $post['title'] }}</div>

        <div class="pbody cl" id="pbody-{{ $id }}">{{ $post['content'] }}</div>
        <button class="readmore" id="readmore-{{ $id }}" onclick="expand({{ $id }})">
            Weiterlesen →
        </button>
    </div>

    {{-- ── Action bar ── --}}
    <div class="pacts">
        @if($mine)
            <span class="pab" style="opacity:.3;cursor:default">
                🤍 <span id="likes-{{ $id }}">{{ $post['likes'] }}</span>
            </span>
            <button class="pab" onclick="deletePost({{ $id }})"
                style="margin-left:auto;color:#c04040;font-size:12px">
                &#x1F5D1; Löschen
            </button>
        @else
            <button class="pab {{ $liked ? 'lk' : '' }}" id="like-btn-{{ $id }}"
                onclick="toggleLike({{ $id }})">
                <span id="like-icon-{{ $id }}">{{ $liked ? '❤️' : '🤍' }}</span>
                <span id="likes-{{ $id }}">{{ $post['likes'] }}</span>
            </button>
        @endif

        <button class="pab" onclick="togC({{ $id }})">
            💬 <span id="cmt-count-{{ $id }}">{{ count($post['comments']) }}</span>
        </button>

        <button class="pab {{ $saved ? 'sv' : '' }}" id="save-btn-{{ $id }}"
            onclick="toggleSave({{ $id }})">
            <span id="save-icon-{{ $id }}">{{ $saved ? '🔖' : '🏷️' }}</span>
            <span id="save-label-{{ $id }}">{{ $saved ? 'Gespeichert' : 'Speichern' }}</span>
        </button>

        <button class="pab" onclick="reportPost({{ $id }})"
            style="margin-left:auto;color:var(--light);font-size:11px">
            &#x26A0; Melden
        </button>

        <span class="sepv"></span>

        @if($mine)
            <div class="stars" style="opacity:.3;pointer-events:none">★★★★★</div>
            <span class="avgr" style="opacity:.5">ø {{ $avg }}</span>
        @else
            <div class="stars" id="stars-{{ $id }}">
                @for($s = 1; $s <= 5; $s++)
                    <span class="star {{ $uRat >= $s ? 'on' : '' }}"
                        onclick="rate({{ $id }}, {{ $s }})">★</span>
                @endfor
            </div>
            <span class="avgr" id="avgr-{{ $id }}">ø {{ $avg }}</span>
        @endif
    </div>

    {{-- ── Comment toggle button ── --}}
    @if(count($post['comments']) > 0)
        <button class="read-cmts-btn" id="cmt-toggle-{{ $id }}" onclick="togC({{ $id }})">
            ▼ Kommentare anzeigen ({{ count($post['comments']) }})
        </button>
    @endif

    {{-- ── Comments section (hidden by default) ── --}}
    <div class="cmts" id="cmts-{{ $id }}" style="display:none">
        <button class="togcmt" onclick="togC({{ $id }})">▲ Kommentare ausblenden</button>

        <div id="cmt-list-{{ $id }}">
            @foreach($post['comments'] as $ci => $comment)
                <div class="cmt" id="cmt-{{ $id }}-{{ $ci }}">
                    <div class="cava">{{ $comment['a'] }}</div>
                    <div class="cbub">
                        <div class="cname" style="display:flex;justify-content:space-between;align-items:center">
                            {{ $comment['n'] }}
                            <button data-owner="{{ $comment['n'] }}"
                                onclick="deleteComment({{ $id }}, {{ $ci }})"
                                style="display:none;background:none;border:none;color:var(--light);font-size:11px;cursor:pointer;padding:0;line-height:1">
                                &#x2715;
                            </button>
                        </div>
                        <div class="ctext">{{ $comment['t'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="cmt-write-label">Kommentar schreiben</div>
        <div class="cform">
            <div class="cava" id="cmt-ava-{{ $id }}">?</div>
            <input class="cinp" id="ci-{{ $id }}"
                placeholder="Anmelden zum Kommentieren"
                readonly
                style="cursor:pointer"
                onclick="checkAuthThen(function(){})"
                onkeydown="if(event.key==='Enter')addC({{ $id }})">
            <button class="csend" onclick="addC({{ $id }})">Senden</button>
        </div>
    </div>

</div>

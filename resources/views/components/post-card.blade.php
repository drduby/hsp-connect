@props(['post'])

@php
    $isModel  = $post instanceof \App\Models\Post;
    $firstTag = $isModel ? $post->tags->first() : null;

    $tagName  = $isModel ? ($firstTag?->name ?? '') : ($post['tag'] ?? '');
    $tc       = $isModel ? ($firstTag?->color ?? 'var(--t)') : ($post['color'] ?? 'var(--t)');
    $typeRaw  = $isModel ? $post->type->value : ($post['type'] ?? '');
    $typeLabel = match($typeRaw) {
        'experience' => 'Erfahrung',
        'question'   => 'Frage',
        default      => $typeRaw,
    };
    $author   = $isModel ? $post->user->nickname : ($post['author'] ?? '');
    $ava      = $isModel ? strtoupper(mb_substr($author, 0, 1)) : ($post['ava'] ?? '');
    $time     = $isModel ? $post->published_at->diffForHumans() : ($post['time'] ?? '');
    $title    = $isModel ? $post->title : ($post['title'] ?? '');
    $content  = $isModel ? $post->content : ($post['content'] ?? '');
    $id       = $post['id'];

    $likes    = $post['likes']    ?? 0;
    $liked    = $post['liked']    ?? false;
    $saved    = $post['saved']    ?? false;
    $mine     = $post['mine']     ?? false;
    $rSum     = $post['rSum']     ?? 0;
    $rCnt     = $post['rCnt']     ?? 0;
    $uRat     = $post['uRat']     ?? 0;
    $avg      = $rCnt > 0 ? number_format($rSum / $rCnt, 1) : '—';
    $comments = $post['comments'] ?? [];
@endphp

<div class="post"
    id="post-{{ $id }}"
    data-type="{{ $typeLabel }}"
    data-tag="{{ $tagName }}"
    data-search="{{ strtolower($title.' '.$content.' '.$tagName) }}"
    style="border-left:3px solid {{ $tc }}">

    {{-- ── Post header & body ── --}}
    <div class="post-inner">
        <div class="pmeta">
            <div class="pava">{{ $ava }}</div>
            <div>
                <div class="pname">{{ $author }}</div>
                <div class="ptime">{{ $time }}</div>
            </div>
            <span class="pbadge {{ $typeLabel === 'Erfahrung' ? 'pb-e' : 'pb-f' }}">
                {{ $typeLabel }}
            </span>
            @if($tagName)
                <span class="ptag"
                    style="border-color:{{ $tc }}30;color:{{ $tc }}"
                    onclick="toggleTag('{{ $tagName }}')">
                    # {{ $tagName }}
                </span>
            @endif
        </div>

        <div class="ptitle">{{ $title }}</div>

        <div class="pbody cl" id="pbody-{{ $id }}">{{ $content }}</div>
        <button class="readmore" id="readmore-{{ $id }}" onclick="expand({{ $id }})">
            Weiterlesen →
        </button>
    </div>

    {{-- ── Action bar ── --}}
    <div class="pacts">
        @if($mine)
            <span class="pab" style="opacity:.3;cursor:default">
                🤍 <span id="likes-{{ $id }}">{{ $likes }}</span>
            </span>
            <button class="pab" onclick="deletePost({{ $id }})"
                style="margin-left:auto;color:#c04040;font-size:12px">
                &#x1F5D1; Löschen
            </button>
        @else
            <button class="pab {{ $liked ? 'lk' : '' }}" id="like-btn-{{ $id }}"
                onclick="toggleLike({{ $id }})">
                <span id="like-icon-{{ $id }}">{{ $liked ? '❤️' : '🤍' }}</span>
                <span id="likes-{{ $id }}">{{ $likes }}</span>
            </button>
        @endif

        <button class="pab" onclick="togC({{ $id }})">
            💬 <span id="cmt-count-{{ $id }}">{{ count($comments) }}</span>
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
    @if(count($comments) > 0)
        <button class="read-cmts-btn" id="cmt-toggle-{{ $id }}" onclick="togC({{ $id }})">
            ▼ Kommentare anzeigen ({{ count($comments) }})
        </button>
    @endif

    {{-- ── Comments section (hidden by default) ── --}}
    <div class="cmts" id="cmts-{{ $id }}" style="display:none">
        <button class="togcmt" onclick="togC({{ $id }})">▲ Kommentare ausblenden</button>

        <div id="cmt-list-{{ $id }}">
            @foreach($comments as $ci => $comment)
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

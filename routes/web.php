<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/reset-password/{token}', function (Request $request, string $token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => $request->query('email'),
    ]);
})->middleware('guest')->name('password.reset');

Route::get('/', function () {
    $tags = ['Spastik', 'Muskeln', 'Entspannung', 'Physiotherapie', 'Hilfsmittel', 'Alltag', 'Ernährung', 'Schlaf', 'Reha'];
    $tagColors = [
        'Spastik' => '#0a6e7a', 'Muskeln' => '#b8762a', 'Entspannung' => '#2a8a52',
        'Physiotherapie' => '#1a5080', 'Hilfsmittel' => '#7a4820', 'Alltag' => '#486070',
        'Ernährung' => '#607020', 'Schlaf' => '#583878', 'Reha' => '#7a3060',
    ];
    $tagCounts = [
        'Spastik' => 48, 'Muskeln' => 31, 'Entspannung' => 27, 'Physiotherapie' => 44,
        'Hilfsmittel' => 22, 'Alltag' => 38, 'Ernährung' => 15, 'Schlaf' => 19, 'Reha' => 29,
    ];
    $names = ['Anna K.', 'Ben M.', 'Clara F.', 'David R.', 'Eva S.', 'Felix T.', 'Greta L.', 'Hannes P.', 'Ida W.', 'Jonas B.', 'Kira N.', 'Leo C.'];
    $avatars = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
    $titlesE = ['Physiotherapie hat wirklich geholfen', 'Neue Hilfsmittel für den Alltag', 'Entspannungstechniken die wirken', 'Erfolge mit Ernährungsumstellung', 'Warum Schlafhygiene alles verändert', '6 Monate Reha — eine Bilanz', 'Wärme gegen Muskelspannung', 'App für Übungen getestet', 'Meine tägliche Routine', 'Tipps für das Büro mit Spastik'];
    $titlesF = ['Wer kennt Spastik-Medikamente?', 'Welche Hilfsmittel sind sinnvoll?', 'Tipps für besseren Schlaf?', 'Wie geht ihr mit Erschöpfung um?', 'Physio Empfehlung Wien?', 'Übungen alleine machbar?'];
    $hours = [1, 2, 3, 5, 7, 11, 14, 18, 22, 24, 36, 48, 72, 96];

    $posts = [];
    for ($i = 0; $i < 14; $i++) {
        $isE = $i % 3 !== 2;
        $pid = $i + 1;
        $tag = $tags[$i % count($tags)];
        $rSum = rand(5, 44);
        $rCnt = rand(2, 11);

        $comments = $i === 0
            ? [
                ['a' => 'A', 'n' => 'Anna K.', 't' => 'Sehr hilfreich, danke! 🙏'],
                ['a' => 'B', 'n' => 'Ben M.', 't' => 'Ähnliche Erfahrungen hier!'],
                ['a' => 'C', 'n' => 'Clara F.', 't' => 'Kannst du mehr über die Übungen erzählen?'],
                ['a' => 'D', 'n' => 'David R.', 't' => 'Nach 3 Wochen merklicher Unterschied!'],
                ['a' => 'E', 'n' => 'Eva S.', 't' => 'Welche Hilfsmittel hast du benutzt?'],
                ['a' => 'F', 'n' => 'Felix T.', 't' => 'Danke — teile das mit meiner Physio.'],
                ['a' => 'G', 'n' => 'Greta L.', 't' => 'Das gibt mir Hoffnung. ❤️'],
                ['a' => 'H', 'n' => 'Hannes P.', 't' => 'Hast du Erfahrungen mit Wärme gemacht?'],
                ['a' => 'I', 'n' => 'Ida W.', 't' => 'Super Beitrag! Mehr davon bitte.'],
                ['a' => 'J', 'n' => 'Jonas B.', 't' => 'Ich würde gerne mehr über deine Routine erfahren.'],
                ['a' => 'K', 'n' => 'Kira N.', 't' => 'Genau mein Thema. Danke! 🌟'],
            ]
            : [
                ['a' => $avatars[($i + 1) % 12], 'n' => $names[($i + 1) % 12], 't' => 'Sehr hilfreich, danke! 🙏'],
                ['a' => $avatars[($i + 2) % 12], 'n' => $names[($i + 2) % 12], 't' => 'Ähnliche Erfahrungen hier!'],
            ];

        $posts[] = [
            'id' => $pid,
            'type' => $isE ? 'Erfahrung' : 'Frage',
            'tag' => $tag,
            'color' => $tagColors[$tag],
            'author' => $names[$i % 12],
            'ava' => $avatars[$i % 12],
            'title' => $isE ? $titlesE[$i % count($titlesE)] : $titlesF[$i % count($titlesF)],
            'content' => $isE
                ? "Nach intensiver Beschäftigung mit {$tag} möchte ich meine Erfahrungen teilen. Es hat mir wirklich geholfen — die wichtigste Erkenntnis: Regelmäßigkeit ist alles. Auch kleine Fortschritte zählen."
                : "Ich suche Erfahrungen zum Thema {$tag}. Hat jemand damit Erfahrungen gemacht? Was hat geholfen, was nicht? Für jeden Tipp dankbar!",
            'likes' => rand(2, 41),
            'liked' => false,
            'saved' => false,
            'mine' => false,
            'rSum' => $rSum,
            'rCnt' => $rCnt,
            'uRat' => 0,
            'time' => 'vor '.$hours[$i].'h',
            'comments' => $comments,
        ];
    }

    return view('home', compact('posts', 'tags', 'tagColors', 'tagCounts'));
});

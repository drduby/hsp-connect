<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\PostsController as AdminPostsController;
use App\Http\Controllers\Admin\ReportsController as AdminReportsController;
use App\Http\Controllers\Admin\TagsController as AdminTagsController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/login', fn () => redirect('/?login=1'))->name('login');

Route::get('/email/verify', fn () => view('auth.verify-email'))
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, string $id, string $hash) {
    $user = User::findOrFail($id);

    abort_unless(
        hash_equals(sha1($user->getEmailForVerification()), $hash) && $request->hasValidSignature(),
        403
    );

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/?verified=1');
})->middleware('signed')->name('verification.verify');

Route::get('/reset-password/{token}', function (Request $request, string $token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => $request->query('email'),
    ]);
})->middleware('guest')->name('password.reset');

Route::post('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['de', 'en'])) {
        session(['locale' => $locale]);
    }

    return back();
})->name('language.switch');

Route::post('/impersonate/stop', function (Request $request) {
    $adminId = $request->session()->pull('impersonating_admin_id');
    abort_unless($adminId, 403);
    Auth::loginUsingId($adminId);
    $request->session()->regenerate();

    return redirect()->route('admin.users.index');
})->middleware('auth')->name('impersonate.stop');

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'show'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store'])->name('login.post');

    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');

        // Tags
        Route::get('/tags', [AdminTagsController::class, 'index'])->name('tags.index');

        // FAQ
        Route::get('/faq', [AdminFaqController::class, 'index'])->name('faq.index');

        // Reports
        Route::get('/reports', [AdminReportsController::class, 'index'])->name('reports.index');

        // Feedback
        Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('feedback.index');

        // Posts
        Route::get('/posts', [AdminPostsController::class, 'index'])->name('posts.index');
    });
});

Route::get('/users/search', function (Request $request) {
    $q = mb_substr($request->query('q', ''), 0, 30);
    if (mb_strlen($q) < 1) {
        return response()->json([]);
    }

    return response()->json(
        User::where('nickname', 'like', $q.'%')
            ->where('id', '!=', auth()->id())
            ->whereNotNull('email_verified_at')
            ->where('is_admin', false)
            ->limit(5)
            ->pluck('nickname')
    );
})->middleware('auth')->name('users.search');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'destroyAll']);
});

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
        ['loc' => route('faq'), 'changefreq' => 'weekly', 'priority' => '0.8'],
    ];

    return response()->view('sitemap', compact('urls'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/help', function () {
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

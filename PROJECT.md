# HSPConnect — Project Documentation

## Overview

HSPConnect is a German-language community platform for people with **Hereditary Spastic Paraplegia (HSP)** — a rare neurological condition. Members can share experiences, ask questions, comment, like, rate, and save posts. The platform includes a separate admin panel for content moderation and management.

- **Live domain**: hsp-connect.com (HTTPS, Let's Encrypt, expires Aug 2026)
- **Local dev URL**: https://hsp-connect.test (served by Laravel Herd)
- **Primary language**: German (DE) — English (EN) also supported
- **Jira project key**: HSPC (workspace: HSP-CONNECT)

---

## Tech Stack

| Layer | Package / Tool | Version |
|---|---|---|
| Language | PHP | ^8.3 |
| Framework | Laravel | ^13.8 |
| Auth | Laravel Fortify | ^1.37 |
| Reactive UI | Livewire | ^4.3 |
| CSS | Tailwind CSS | v4 |
| Testing | Pest | ^4.7 |
| Code style | Laravel Pint | ^1.27 |
| Dev server | Laravel Herd | — |
| Asset bundling | Vite | — |
| DB (local) | SQLite | — |

Dev-only: `laravel/boost` (MCP), `laravel/pail` (log tailing), `laravel/pao`.

---

## Directory Structure

```
app/
  Actions/Fortify/         # User creation, password reset, profile update
  Enums/PostType.php       # experience | question
  Http/
    Controllers/
      Admin/               # Dashboard, Users, Tags, FAQ, Posts, Reports, Feedback, Translations, Logs, Password
      FaqController.php
      NotificationController.php
      PostController.php   # Home feed (index only)
    Middleware/
      EnsureUserIsAdmin.php
      SecurityHeaders.php
      SetLocale.php
      TrackLastSeen.php
  Models/
    ActivityLog.php
    Comment.php
    FaqItem.php
    FaqQuestion.php        # Inline user-submitted FAQ questions (different from FaqItem)
    Feedback.php
    Post.php               # SoftDeletes; type cast to PostType enum
    PostReport.php
    PostTag.php
    Tag.php
    User.php
  Notifications/
    CommentPosted.php      # database channel
    PostLiked.php          # database channel
    UserMentioned.php      # database channel
  Services/
    ActivityLogger.php     # Static ::log() — never throws
    PostService.php        # filteredCounts() + publishedPostsPaginated()
    TagService.php         # activeTags()

database/migrations/       # Chronological — see "Database Schema" section
lang/
  de/ui.php                # All German UI strings (single file, nested arrays)
  en/ui.php                # All English UI strings (same structure)

resources/
  js/
    app.js                 # Entry point — wires all modules to window.*
    auth.js
    feed.js
    hexbg.js               # Animated hexagon canvas background
    modals.js              # openInfo(), openPM(), openConfirm(), etc.
    notifications.js
    posts.js               # Like, save, rate, comment, delete actions
    profile.js
    state.js               # Shared { loggedIn, currentUser } singleton
    utils.js               # toast(), checkAuthThen()
  views/
    admin/                 # Plain Blade views (no Livewire) for admin layout
    auth/                  # reset-password, verify-email
    components/
      ⚡*.blade.php         # Livewire SFC (single-file components)
      admin/⚡*.blade.php   # Admin Livewire SFCs
      modals/              # Static Blade modal partials
      footer.blade.php
      header.blade.php
      sidebar.blade.php
    layouts/
      app.blade.php        # Main public layout
      admin.blade.php      # Admin layout
    home.blade.php
    faq.blade.php
```

---

## Database Schema

### users
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| first_name | string | |
| last_name | string | |
| nickname | string unique | used for display; letters/numbers/_ only |
| email | string unique | lowercased by Fortify |
| email_verified_at | timestamp | nullable |
| password | string | hashed |
| is_admin | boolean | default false |
| blocked_at | timestamp | nullable — non-null = blocked |
| last_seen_at | timestamp | updated by TrackLastSeen middleware (5-min cache) |
| last_login_at | timestamp | |
| two_factor_* | — | Fortify 2FA columns |

### posts
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users | |
| title | string | |
| content | text | |
| type | string | `experience` or `question` (PostType enum) |
| is_published | boolean | |
| published_at | timestamp | |
| deleted_at | timestamp | SoftDeletes |

### tags
`id, name, name_en, slug, color (hex), is_active`
Localized via `$tag->localized_name` accessor (returns `name_en` if locale = en and non-empty, else `name`).

### pivot tables
- `post_tag` — many-to-many posts ↔ tags
- `post_likes` — user_id, post_id, timestamps
- `post_saves` — user_id, post_id, timestamps
- `post_ratings` — user_id, post_id, rating (int), timestamps

### comments
`id, post_id, user_id, content, parent_id (nullable — for replies)`

### faq_items
`id, question, question_en, answer, answer_en, tags (JSON), sort_order, is_published`
Localized via `localized_question` / `localized_answer` accessors.

### feedbacks
`id, user_id (nullable), type (idea|bug), topic, description, email (nullable)`

### activity_logs
`id, user_id (nullable), action, description, ip_address`
Written via `ActivityLogger::log()` — silently ignores exceptions.

### notifications (Laravel default)
`id, type, notifiable_type, notifiable_id, data (JSON), read_at`
Used for: `CommentPosted`, `PostLiked`, `UserMentioned`.

### post_reports
`id, post_id, user_id, reason`

---

## Authentication

- **Provider**: Laravel Fortify (views = false — custom Blade views)
- **Registration**: first_name, last_name, nickname, email, password (min 8, letters+numbers)
- **Email verification**: required before posting/commenting
- **2FA**: TOTP-based (enabled, requires password confirmation)
- **Passkeys**: WebAuthn via `laravel/passkeys` (relying party = app URL host)
- **Password reset**: Email link → `/reset-password/{token}` custom view
- **Login route**: `/login` redirects to `/?login=1` which triggers the JS auth modal
- **Admin login**: Separate form at `/admin/login` (session-based, no Fortify)

### Admin auth flow
1. `POST /admin/login` → `AdminLoginController@store` — validates email + password + `is_admin = true`
2. Session stores admin user; `EnsureUserIsAdmin` middleware guards all `/admin/*` routes
3. **Impersonation**: Admin can log in as any user. Session stores `impersonating_admin_id`. `POST /impersonate/stop` restores admin session.

---

## Localisation

- **Languages**: `de` (default), `en`
- **Switching**: `POST /language/{locale}` saves `session('locale')`, redirects back
- **Middleware**: `SetLocale` reads `session('locale', 'de')` and calls `App::setLocale()`
- **Source of truth**: `lang/de/ui.php` and `lang/en/ui.php` — nested PHP arrays, single file per locale
- **Admin editing**: `admin/translations` Livewire SFC flattens keys via `Arr::dot()`, shows searchable table, inline-edits, writes back via `file_put_contents` + `opcache_invalidate`

### JS globals injected in `app.blade.php`
| Variable | Contents |
|---|---|
| `window.__AUTH__` | `{name, ava}` or `null` |
| `window.__NOTIF_COUNT__` | unread notification count |
| `window.__LOCALE__` | `'de'` or `'en'` |
| `window.__LEGAL__` | All `legal.*` keys from lang file (see below) |
| `window.__TRANS__` | Misc UI strings (notifications, filters, etc.) |
| `window.__TAGS__` | Injected per-page from `home.blade.php` |
| `window.__SAVED_COUNT__` | Count of saved posts for current user |
| `window.__MY_POST_COUNT__` | Count of user's own posts |
| `window.__LIKES_GIVEN__` | Count of likes given by current user |

### Legal modals (HSPC-97)
Footer links (`openInfo('impressum')`, etc.) read from `window.__LEGAL__`, which is built from `lang/{locale}/ui.php` under the `legal` key:
- `legal.impressum`, `legal.datenschutz`, `legal.nutzung`, `legal.regeln`, `legal.faq`, `legal.kontakt`
- Each has `.title` and `.body` (HTML string)
- Admin can edit via `admin/translations` — search "legal.impressum.body" etc.

---

## Routing

### Public
| Method | URI | Name | Handler |
|---|---|---|---|
| GET | `/` | `home` | `PostController@index` → `home.blade.php` |
| GET | `/faq` | `faq` | `FaqController@index` |
| GET | `/help` | — | Static demo page (fake posts, no DB) |
| GET | `/sitemap.xml` | `sitemap` | Inline closure |
| POST | `/language/{locale}` | `language.switch` | Session locale switch |
| GET | `/reset-password/{token}` | `password.reset` | Custom view |
| GET | `/email/verify` | `verification.notice` | verify-email view |
| GET | `/email/verify/{id}/{hash}` | `verification.verify` | Signed URL handler |

### Auth (middleware: auth + verified)
| Method | URI | Handler |
|---|---|---|
| GET | `/notifications` | `NotificationController@index` |
| POST | `/notifications/{id}/read` | mark read |
| DELETE | `/notifications/{id}` | destroy one |
| DELETE | `/notifications` | destroy all |
| GET | `/users/search` | nickname autocomplete (JSON) |
| POST | `/impersonate/stop` | `impersonate.stop` |

Fortify registers its own routes (login POST, register POST, password routes, 2FA, passkeys).

### Admin (`/admin/*`, middleware: `admin`)
| URI | Name |
|---|---|
| GET `/admin/` | `admin.dashboard` |
| GET `/admin/users` | `admin.users.index` |
| GET `/admin/tags` | `admin.tags.index` |
| GET `/admin/faq` | `admin.faq.index` |
| GET `/admin/reports` | `admin.reports.index` |
| GET `/admin/feedback` | `admin.feedback.index` |
| GET `/admin/posts` | `admin.posts.index` |
| GET `/admin/translations` | `admin.translations.index` |
| GET `/admin/logs` | `admin.logs.index` |
| GET `/admin/password` | `admin.password.index` |

All admin CRUD actions (edit, delete, block, etc.) are handled inside Livewire SFCs via `wire:click` / `wire:submit`.

---

## Livewire Components

All SFCs use the `⚡` naming prefix and live in `resources/views/components/`.

### Public SFCs
| File | Class / Purpose |
|---|---|
| `⚡auth-modal.blade.php` | Login + Register tabs, dispatches `open-auth-modal` |
| `⚡create-post.blade.php` | New post form (title, content, type, tags) |
| `⚡post-feed.blade.php` | Feed with search, type filter, tag filter, view (all/saved/mine), pagination |
| `⚡post-card.blade.php` | Individual post (likes, saves, ratings, comments, delete) |
| `⚡faq-question.blade.php` | Inline user-submitted FAQ question form |
| `⚡feedback-modal.blade.php` | Feedback form (idea/bug) |
| `⚡profile-editor.blade.php` | Edit nickname, first/last name, bio, avatar |
| `⚡report-post.blade.php` | Report post modal |

### Admin SFCs
| File | Purpose |
|---|---|
| `⚡users.blade.php` | User list, search, block/unblock, impersonate, edit |
| `⚡tags.blade.php` | Tag CRUD (name DE+EN, slug, color, active toggle) |
| `⚡faq.blade.php` | FAQ item CRUD (DE+EN question+answer, tags, sort_order) |
| `⚡posts.blade.php` | Post moderation (view, publish/unpublish, delete) |
| `⚡reports.blade.php` | Review and resolve post reports |
| `⚡feedback.blade.php` | View user feedback (ideas and bugs) |
| `⚡translations.blade.php` | Edit lang files inline (Arr::dot flattened, file_put_contents) |
| `⚡logs.blade.php` | Activity log viewer with search |
| `⚡password.blade.php` | Admin password change |

---

## Middleware Stack

| Alias | Class | Applied To |
|---|---|---|
| `web` (default) | Laravel default web group | All web routes |
| `SetLocale` | `App\Http\Middleware\SetLocale` | Global (in web group) |
| `TrackLastSeen` | `App\Http\Middleware\TrackLastSeen` | Global — updates `last_seen_at` every 5 min via cache |
| `SecurityHeaders` | `App\Http\Middleware\SecurityHeaders` | Global — CSP, X-Frame-Options, HSTS, etc. |
| `admin` | `EnsureUserIsAdmin` | All `/admin/*` routes (except `/admin/login`) |

---

## Security

- Security headers via `SecurityHeaders` middleware: CSP, X-Frame-Options (SAMEORIGIN), HSTS (on HTTPS), X-Content-Type-Options, Referrer-Policy, Permissions-Policy
- HTTP Security Grade: A (live score ~82/100 after pentest + fixes)
- Rate limiting on Fortify routes: login, register, 2FA, passkeys
- User blocking: `blocked_at` timestamp; blocked users cannot post or comment
- Post reporting: users can report posts → admin reviews in `admin/reports`
- CSRF protection on all forms

---

## Frontend Architecture

- **No SPA framework** — Livewire handles reactive parts; vanilla JS for everything else
- **Alpine.js** available (bundled with Livewire)
- **Vite** bundles `resources/css/app.css` (Tailwind v4) and `resources/js/app.js`
- **Hex canvas background** (`hexbg.js`) — animated, persistent across navigations via `@persist`
- **Livewire navigate** (`wire:navigate`) used for SPA-like page transitions between home and FAQ
- **Toast notifications** — `toast(msg, type)` in `utils.js`
- **Confirm modal** — `openConfirm(title, msg, callback)` generic confirmation dialog

### JS module structure
```
app.js (entry)
  ├── state.js       — { loggedIn, currentUser }
  ├── utils.js       — toast(), checkAuthThen()
  ├── hexbg.js       — canvas animation
  ├── auth.js        — openLg/closeLg, setLoggedInUI
  ├── feed.js        — render(), go(), toggleTag(), filter logic
  ├── modals.js      — openInfo(), openPM(), openConfirm(), openFeedback()
  ├── notifications.js
  ├── posts.js       — like, save, rate, comment, delete (AJAX to Livewire)
  └── profile.js     — openProfileMenu, openAccountPage
```

---

## Conventions

### DE/EN bilingual models
Models with user-facing text in DB use `field` + `field_en` columns:
- `tags`: `name`, `name_en`
- `faq_items`: `question`, `question_en`, `answer`, `answer_en`

Each has a `getLocalized*Attribute` accessor: returns `_en` if locale = `en` and non-empty, else falls back to German.

### Lang file text (non-DB content)
All static UI text lives in `lang/de/ui.php` and `lang/en/ui.php`. Keys are nested arrays accessed via dot notation: `__('ui.footer.imprint')`. Admin can edit via `admin/translations`.

### Controller convention — required view variables
Every controller passing data to a view that extends `layouts/app.blade.php` must pass `$counts`, `$memberCount`, and `$onlineCount` (used by the sidebar/header for live stats).

### Blade gotcha — `@json()` with function calls
When building a PHP array containing `__()` calls for `@json()`, assign to a variable first in a `@php` block to avoid Blade parser errors:
```blade
@php
$arr = ['key' => __('some.key')];
@endphp
window.__VAR__ = @json($arr);
```

### Pint
After editing any PHP file, run `vendor/bin/pint --dirty --format agent` before committing.

---

## Testing

- **Framework**: Pest v4 + `pestphp/pest-plugin-laravel`
- **Test location**: `tests/Feature/` (Feature) and `tests/Unit/` (Unit)
- **Database**: `RefreshDatabase` for tests that hit the DB (HTTP tests, model tests)
- **Locale**: `app()->setLocale('de')` inside test body (Pest `.locale()` chaining does not exist in this project)
- **Run tests**: `php artisan test --compact` or with filter: `php artisan test --compact --filter=LegalTranslations`
- **Existing test file**: `tests/Feature/LegalTranslationsTest.php` — 8 tests covering legal translation keys and `window.__LEGAL__` injection

---

## Jira

- **Workspace**: HSP-CONNECT
- **Cloud ID**: `9d6c21f9-65aa-4df3-8c4d-affbec9f1edd`
- **Project key**: `HSPC`
- Tickets: HSPC-97 (Impressum DE&EN — implemented), HSPC-98 through HSPC-102 (To Do)

---

## Key Decisions & Gotchas

1. **Legal texts in lang files, not DB** — admin edits via translations panel (not a dedicated DB table)
2. **`window.__LEGAL__`** is the bridge between PHP lang files and JS modal rendering
3. **`/help` route** returns a static demo page with fake posts (no DB) — used for marketing/preview
4. **`TrackLastSeen`** uses a 5-minute cache key per user to avoid DB writes on every request
5. **`ActivityLogger::log()`** wraps DB write in try/catch — never crashes the app on logging failure
6. **Admin-only auth** is a separate session, not Fortify — validated manually in `AdminLoginController`
7. **Impersonation** stores admin ID in session as `impersonating_admin_id`; displayed as a sticky banner
8. **`PostService`** computes `user_liked`, `user_saved`, `user_rating`, `rating_average`, `is_mine` on each post after paginating — avoids N+1 by using eager-loaded collections

# Auth, Breeze & Policies — Full Step-by-Step Overview

> Note: Laravel Breeze is **not installed** as a package (`composer.json` has no `laravel/breeze`).
> Auth was wired manually using Laravel's built-in session guard + Eloquent provider.

---

## Step 1 — Auth Configuration

`config/auth.php`

Defines the default guard (`web`) using sessions, backed by the `User` Eloquent model.

```php
// config/auth.php

'defaults' => [
    'guard'     => env('AUTH_GUARD', 'web'),
    'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
],

'guards' => [
    'web' => [
        'driver'   => 'session',   // stores auth state in the session
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model'  => env('AUTH_MODEL', User::class), // App\Models\User
    ],
],

'passwords' => [
    'users' => [
        'provider' => 'users',
        'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
        'expire'   => 60,    // token valid for 60 minutes
        'throttle' => 60,    // 60 seconds between reset requests
    ],
],
```

---

## Step 2 — Users Table Migration (role column)

`database/migrations/2026_04_18_090417_create_users_table.php`

The `role` enum column is the foundation of the entire authorization system.

```php
// database/migrations/2026_04_18_090417_create_users_table.php

Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('role', ['admin', 'user'])->default('user'); // <-- drives all auth logic
    $table->rememberToken();
    $table->timestamps();
});
```

---

## Step 3 — User Model

`app/Models/User.php`

Extends `Authenticatable` (required for Laravel's auth system). The `isAdmin()` helper is used by the middleware, Gate, and Blade views.

```php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',  // auto-hashes on set
        ];
    }

    public function posts()    { return $this->hasMany(Post::class); }
    public function comments() { return $this->hasMany(Comment::class); }

    // Used everywhere: middleware, Gate, Blade
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
```

---

## Step 4 — Admin Middleware

`app/Http/Middleware/AdminMiddleware.php`

Blocks any request from non-admin users with a 403.

```php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Step 1: must be logged in
        // Step 2: must have role = 'admin'
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Accès refusé. Vous devez être administrateur.');
        }

        return $next($request);
    }
}
```

---

## Step 5 — Registering the Middleware Alias

`bootstrap/app.php`

The `admin` alias is registered here so it can be used in route definitions.

```php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class, // <-- alias registered
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})
    ->create();
```

---

## Step 6 — Gate Definition (Admin Override)

`app/Providers/AppServiceProvider.php`

The `delete-any-post` Gate lets admins delete any post, bypassing ownership checks in the policy.

```php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Admins can delete any post, regardless of who owns it
        Gate::define('delete-any-post', function ($user) {
            return $user->isAdmin();
        });
    }
}
```

---

## Step 7 — PostPolicy (Ownership Checks)

`app/Policies/PostPolicy.php`

Handles fine-grained per-resource authorization. Auto-discovered by Laravel — no manual registration needed.

```php
// app/Policies/PostPolicy.php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    // Any authenticated user can list or view posts
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Post $post): bool { return true; }

    // Any authenticated user can create a post
    public function create(User $user): bool { return true; }

    // Only the post owner can edit
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // Only the post owner can delete (admins use the Gate instead)
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // Soft delete restore — disabled
    public function restore(User $user, Post $post): bool     { return false; }

    // Hard delete — disabled
    public function forceDelete(User $user, Post $post): bool { return false; }
}
```

---

## Step 8 — PostController (Auth + Policy in Action)

`app/Http/Controllers/PostController.php`

This is where all the auth layers come together.

```php
// app/Http/Controllers/PostController.php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    // Constructor-level middleware: all actions require auth except index & show
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    // PUBLIC — no auth needed
    public function index()
    {
        $posts = Post::with(['user', 'comments'])->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    // PUBLIC — no auth needed
    public function show(Post $post)
    {
        $post->load(['user', 'comments.user', 'tags']);
        return view('posts.show', compact('post'));
    }

    // AUTH REQUIRED — policy: any authenticated user can create
    public function create()
    {
        $this->authorize('create', Post::class);
        return view('posts.create');
    }

    // AUTH REQUIRED — validates input, creates post under current user
    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = $request->user()->posts()->create($validated);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post créé avec succès!');
    }

    // AUTH REQUIRED — policy: only owner can edit
    public function edit(Post $post)
    {
        $this->authorize('update', $post); // throws 403 if not owner
        return view('posts.edit', compact('post'));
    }

    // AUTH REQUIRED — policy: only owner can update
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($validated);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post mis à jour avec succès!');
    }

    // AUTH REQUIRED — Gate (admin) OR policy (owner) can delete
    public function destroy(Post $post)
    {
        if (Gate::allows('delete-any-post') || $this->authorize('delete', $post)) {
            $post->delete();
            return redirect()->route('posts.index')
                ->with('success', 'Post supprimé avec succès!');
        }

        abort(403);
    }
}
```

---

## Step 9 — Routes (Three Access Tiers)

`routes/web.php`

```php
// routes/web.php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// ── TIER 1: Public ────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('posts.index'));
Route::get('/posts',        [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// ── TIER 2: Authenticated users ───────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/posts/create',      [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts',            [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}',      [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}',   [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/dashboard', function () {
        $posts = auth()->user()->posts()->with('comments')->latest()->get();
        return view('dashboard', compact('posts'));
    })->name('dashboard');
});

// ── TIER 3: Admins only (auth + admin middleware) ─────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        $stats = [
            'total_users'    => \App\Models\User::count(),
            'total_posts'    => \App\Models\Post::count(),
            'total_comments' => \App\Models\Comment::count(),
            'recent_posts'   => \App\Models\Post::with('user')->latest()->take(5)->get(),
        ];
        return view('admin.dashboard', compact('stats'));
    })->name('dashboard');

    Route::get('/users', function () {
        $users = \App\Models\User::withCount('posts')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    })->name('users');
});
```

---

## Step 10 — Auth-Aware Layout (Blade)

`resources/views/layouts/app.blade.php`

The nav conditionally renders based on auth state and role.

```blade
{{-- resources/views/layouts/app.blade.php --}}

<nav>
    <div class="container">
        <div>
            <a href="{{ route('posts.index') }}">Blog</a>

            @auth
                {{-- Only shown to logged-in users --}}
                <a href="{{ route('posts.create') }}">Créer un post</a>

                @if(auth()->user()->isAdmin())
                    {{-- Only shown to admins --}}
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
            @endauth
        </div>

        <div>
            @auth
                <span>{{ auth()->user()->name }}</span>

                {{-- Logout must be POST + CSRF protected --}}
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn">Déconnexion</button>
                </form>
            @else
                {{-- Shown to guests only --}}
                <a href="{{ route('login') }}">Connexion</a>
                <a href="{{ route('register') }}">Inscription</a>
            @endauth
        </div>
    </div>
</nav>

{{-- Flash messages --}}
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>
```

---

## Full Auth Flow

```
Incoming Request
│
├── /posts  or  /posts/{id}
│     └── Public — no checks, straight to controller
│
├── /posts/create  /posts/{id}/edit  etc.
│     └── auth middleware
│           ├── not logged in → redirect to /login
│           └── logged in → PostController
│                               └── $this->authorize('update'/'delete', $post)
│                                         ├── owner (user_id match) → allowed
│                                         └── not owner → 403
│
├── /posts/{id} DELETE
│     └── auth middleware → PostController::destroy()
│                               ├── Gate::allows('delete-any-post') → admin? → allowed
│                               └── $this->authorize('delete', $post) → owner? → allowed
│                                                                         else → 403
│
└── /admin/*
      └── auth middleware → AdminMiddleware
                                ├── not logged in → 403
                                ├── not admin     → 403
                                └── admin         → admin route/view
```

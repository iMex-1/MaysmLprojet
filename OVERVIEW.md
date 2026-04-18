# Auth, Breeze & Policies — Overview

---

## 1. Authentication Setup

Laravel Breeze is **not installed** as a package here. Auth was wired manually using Laravel's built-in auth system (session guard + Eloquent provider).

`config/auth.php` — default guard uses sessions with the `User` model:
```php
'defaults' => [
    'guard'     => 'web',       // session-based
    'passwords' => 'users',
],

'guards' => [
    'web' => [
        'driver'   => 'session',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model'  => User::class,
    ],
],
```

The `login`, `logout`, and `register` routes are expected to exist (referenced in the layout), likely provided by a manual auth scaffold or `Auth::routes()`.

---

## 2. The User Model & Role System

The `role` column on `users` drives all authorization logic.

Migration:
```php
$table->enum('role', ['admin', 'user'])->default('user');
```

Model helper:
```php
public function isAdmin(): bool
{
    return $this->role === 'admin';
}
```

This single method is the foundation for both the middleware and the Gate.

---

## 3. Protecting Routes with `auth` Middleware

Routes are split into three tiers:

```php
// Tier 1 — public, no auth needed
Route::get('/posts',        [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Tier 2 — any authenticated user
Route::middleware(['auth'])->group(function () {
    Route::get('/posts/create',      [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts',            [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}',      [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}',   [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/dashboard',         ...)->name('dashboard');
});

// Tier 3 — admins only (auth + admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', ...)->name('admin.dashboard');
    Route::get('/users',     ...)->name('admin.users');
});
```

The controller also restricts at the constructor level as a second layer:
```php
public function __construct()
{
    $this->middleware('auth')->except(['index', 'show']);
}
```

---

## 4. AdminMiddleware

Registered as the `admin` alias in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

The middleware itself:
```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403, 'Accès refusé. Vous devez être administrateur.');
    }
    return $next($request);
}
```

It checks two things in order:
1. Is the user logged in at all?
2. Does `isAdmin()` return `true`?

If either fails → `403`.

---

## 5. PostPolicy

Handles fine-grained ownership checks. Registered automatically via Laravel's auto-discovery.

```php
class PostPolicy
{
    // Any authenticated user can view posts
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Post $post): bool { return true; }

    // Any authenticated user can create
    public function create(User $user): bool { return true; }

    // Only the post owner can edit
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // Only the post owner can delete (admins bypass this via Gate)
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function restore(User $user, Post $post): bool    { return false; }
    public function forceDelete(User $user, Post $post): bool { return false; }
}
```

Used in the controller with `$this->authorize()`:
```php
public function edit(Post $post)
{
    $this->authorize('update', $post); // throws 403 if not owner
    return view('posts.edit', compact('post'));
}

public function update(Request $request, Post $post)
{
    $this->authorize('update', $post);
    // ...
}
```

---

## 6. Gate — Admin Override for Delete

Defined in `AppServiceProvider::boot()`, this Gate lets admins delete any post, bypassing the ownership check in `PostPolicy`:

```php
Gate::define('delete-any-post', function ($user) {
    return $user->isAdmin();
});
```

Used in `destroy()` alongside the policy:
```php
public function destroy(Post $post)
{
    // Admin can delete anything, owner can delete their own
    if (Gate::allows('delete-any-post') || $this->authorize('delete', $post)) {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post supprimé!');
    }
    abort(403);
}
```

---

## 7. Layout — Auth-Aware Navigation

The Blade layout conditionally renders nav links based on auth state and role:

```blade
@auth
    <a href="{{ route('posts.create') }}">Créer un post</a>

    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}">Admin</a>
    @endif

    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn">Déconnexion</button>
    </form>
@else
    <a href="{{ route('login') }}">Connexion</a>
    <a href="{{ route('register') }}">Inscription</a>
@endauth
```

- `@auth` / `@else` — shows different nav for guests vs logged-in users
- `isAdmin()` check — only admins see the Admin link
- Logout uses a `POST` form with `@csrf` as required by Laravel

---

## 8. Auth Flow Summary

```
Request
  │
  ├── public route?  ──────────────────────────────► Controller (no checks)
  │
  ├── auth middleware ──► not logged in? ──► redirect to /login
  │        │
  │        └── logged in ──► Controller
  │                              │
  │                              └── authorize() ──► PostPolicy
  │                                      │
  │                                      ├── owner? ──► allowed
  │                                      └── not owner? ──► 403
  │
  └── admin middleware ──► not admin? ──► 403
           │
           └── admin ──► Admin Controller / closure
```

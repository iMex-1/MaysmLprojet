# Project Overview

A Laravel blog application with authentication, roles, posts, comments, and tags.

---

## 1. Migrations

5 custom migrations created on `2026-04-18`, run in this order:

### users
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('role', ['admin', 'user'])->default('user'); // role-based access
    $table->rememberToken();
    $table->timestamps();
});
```

### posts
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('content');
    $table->timestamps();
});
```

### comments
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->text('content');
    $table->timestamps();
});
```

### tags
```php
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->timestamps();
});
```

### post_tag (pivot)
```php
Schema::create('post_tag', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
    $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
    $table->timestamps();
});
```

---

## 2. Models & Relationships

### User
```php
#[Fillable(['name', 'email', 'password', 'role'])]
class User extends Authenticatable
{
    public function posts()    { return $this->hasMany(Post::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function isAdmin(): bool { return $this->role === 'admin'; }
}
```

### Post
```php
class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'content'];

    public function user(): BelongsTo       { return $this->belongsTo(User::class); }
    public function comments(): HasMany     { return $this->hasMany(Comment::class); }
    public function tags(): BelongsToMany   { return $this->belongsToMany(Tag::class); }
}
```

### Comment
```php
class Comment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'content'];

    public function post(): BelongsTo { return $this->belongsTo(Post::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
```

### Tag
```php
class Tag extends Model
{
    protected $fillable = ['name'];

    public function posts(): BelongsToMany { return $this->belongsToMany(Post::class); }
}
```

Relationship map:
```
User ──< Post ──< Comment >── User
              └──< Tag (many-to-many via post_tag)
```

---

## 3. Controllers

### PostController — CRUD with authorization

`index` and `show` are public. Everything else requires `auth`.

```php
public function __construct()
{
    $this->middleware('auth')->except(['index', 'show']);
}
```

Eager loading on `index` to avoid N+1:
```php
public function index()
{
    $posts = Post::with(['user', 'comments'])->latest()->paginate(10);
    return view('posts.index', compact('posts'));
}
```

Policy authorization on write actions:
```php
public function store(Request $request)
{
    $this->authorize('create', Post::class);

    $validated = $request->validate([
        'title'   => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    $post = $request->user()->posts()->create($validated);
    return redirect()->route('posts.show', $post)->with('success', 'Post créé!');
}
```

Admins can delete any post via Gate, owners can delete their own:
```php
public function destroy(Post $post)
{
    if (Gate::allows('delete-any-post') || $this->authorize('delete', $post)) {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post supprimé!');
    }
    abort(403);
}
```

### UserController — deep eager loading
```php
public function show($id)
{
    $user = User::with(['posts.comments.user'])->findOrFail($id);
    return view('users.show', compact('user'));
}
```

---

## 4. Middleware

### AdminMiddleware
Registered as `admin` alias, blocks non-admin users with a 403.

```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403, 'Accès refusé. Vous devez être administrateur.');
    }
    return $next($request);
}
```

---

## 5. Policies & Gates

### PostPolicy
```php
public function update(User $user, Post $post): bool
{
    return $user->id === $post->user_id; // only the owner
}

public function delete(User $user, Post $post): bool
{
    return $user->id === $post->user_id; // only the owner (admins use the Gate)
}

public function create(User $user): bool { return true; } // any authenticated user
public function view(User $user, Post $post): bool { return true; } // always visible
```

### Gate: `delete-any-post`
Defined in `AppServiceProvider::boot()` — lets admins delete any post regardless of ownership.

```php
Gate::define('delete-any-post', function ($user) {
    return $user->isAdmin();
});
```

---

## 6. Routes

```php
// Public
Route::get('/', fn() => redirect()->route('posts.index'));
Route::get('/posts',        [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/posts/create',        [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts',              [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit',   [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}',        [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}',     [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/dashboard',           fn() => view('dashboard', [...]))->name('dashboard');
});

// Admins only
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', ...)->name('dashboard');
    Route::get('/users',     ...)->name('users');
});
```

| URI | Access |
|-----|--------|
| `/posts` | public |
| `/posts/{post}` | public |
| `/posts/create` | auth |
| `/posts/{post}/edit` | auth + owner |
| `/dashboard` | auth |
| `/admin/dashboard` | auth + admin |
| `/admin/users` | auth + admin |

---

## 7. Seeders

### DatabaseSeeder
```php
public function run(): void
{
    // Fixed accounts for testing
    User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'role' => 'admin']);
    User::create(['name' => 'User',  'email' => 'user@example.com',  'password' => bcrypt('password'), 'role' => 'user']);

    User::factory(3)->create(); // 3 random users

    $this->call(PostSeeder::class);
}
```

### PostSeeder
```php
// Creates 4 tags
$tags = collect(['Laravel', 'PHP', 'Web Development', 'Tutorial'])
    ->map(fn($name) => Tag::create(['name' => $name]));

// Each user gets 3 posts, each post gets random tags + random comments
foreach ($users as $user) {
    for ($i = 1; $i <= 3; $i++) {
        $post = Post::create([...]);
        $post->tags()->attach($tags->random()->id);  // attach random tag
        // random comments from other users
        foreach ($users as $commenter) {
            if (rand(0, 1)) Comment::create([...]);
        }
    }
}
```

Run with:
```bash
php artisan migrate --seed
# or separately
php artisan migrate
php artisan db:seed
```

---

## 8. Views

| View | Description |
|------|-------------|
| `posts/index` | Paginated post list with author and comment count |
| `posts/show` | Full post with comments, tags, and edit/delete buttons |
| `posts/create` | Form to create a new post |
| `posts/edit` | Form to edit an existing post |
| `dashboard` | Authenticated user's own posts |
| `admin/dashboard` | Stats: total users, posts, comments + recent posts |
| `admin/users` | Paginated user list with post counts |

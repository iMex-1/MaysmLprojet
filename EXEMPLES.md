# Exemples d'utilisation

## Afficher tous les posts d'un utilisateur avec leurs commentaires

### Dans un contrôleur

```php
use App\Models\User;

// Récupérer un utilisateur avec ses posts et commentaires
$user = User::with(['posts.comments.user'])->find(1);

// Afficher les posts
foreach ($user->posts as $post) {
    echo "Post: " . $post->title . "\n";
    echo "Commentaires: " . $post->comments->count() . "\n";
    
    foreach ($post->comments as $comment) {
        echo "  - " . $comment->user->name . ": " . $comment->content . "\n";
    }
}
```

### Dans une route

```php
Route::get('/user/{user}/posts', function (User $user) {
    $posts = $user->posts()->with(['comments.user', 'tags'])->get();
    return view('user.posts', compact('user', 'posts'));
});
```

### Dans une vue Blade

```blade
<h1>Posts de {{ $user->name }}</h1>

@foreach($user->posts as $post)
    <div class="post">
        <h2>{{ $post->title }}</h2>
        <p>{{ $post->content }}</p>
        
        <h3>Commentaires ({{ $post->comments->count() }})</h3>
        @foreach($post->comments as $comment)
            <div class="comment">
                <strong>{{ $comment->user->name }}</strong>
                <p>{{ $comment->content }}</p>
                <small>{{ $comment->created_at->diffForHumans() }}</small>
            </div>
        @endforeach
    </div>
@endforeach
```

## Utilisation des relations

### Créer un post avec des tags

```php
use App\Models\Post;
use App\Models\Tag;

// Créer un post
$post = auth()->user()->posts()->create([
    'title' => 'Mon nouveau post',
    'content' => 'Contenu du post...',
]);

// Attacher des tags
$tag1 = Tag::firstOrCreate(['name' => 'Laravel']);
$tag2 = Tag::firstOrCreate(['name' => 'PHP']);

$post->tags()->attach([$tag1->id, $tag2->id]);
```

### Créer un commentaire

```php
use App\Models\Post;

$post = Post::find(1);

$post->comments()->create([
    'user_id' => auth()->id(),
    'content' => 'Super article !',
]);
```

### Récupérer les posts avec leurs relations

```php
// Tous les posts avec utilisateur, commentaires et tags
$posts = Post::with(['user', 'comments.user', 'tags'])->get();

// Posts d'un utilisateur spécifique
$userPosts = Post::where('user_id', 1)
    ->with(['comments', 'tags'])
    ->latest()
    ->get();

// Posts avec au moins un commentaire
$postsWithComments = Post::has('comments')
    ->with(['comments.user'])
    ->get();

// Posts avec un tag spécifique
$laravelPosts = Post::whereHas('tags', function ($query) {
    $query->where('name', 'Laravel');
})->get();
```

## Utilisation des Policies

### Dans un contrôleur

```php
public function edit(Post $post)
{
    // Vérifier l'autorisation
    $this->authorize('update', $post);
    
    return view('posts.edit', compact('post'));
}

public function destroy(Post $post)
{
    // Vérifier avec Gate ou Policy
    if (Gate::allows('delete-any-post') || $this->authorize('delete', $post)) {
        $post->delete();
        return redirect()->route('posts.index');
    }
    
    abort(403);
}
```

### Dans une vue Blade

```blade
{{-- Afficher le bouton uniquement si autorisé --}}
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}" class="btn">Modifier</a>
@endcan

{{-- Vérifier plusieurs conditions --}}
@if(Gate::allows('delete-any-post') || Gate::allows('delete', $post))
    <form action="{{ route('posts.destroy', $post) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Supprimer</button>
    </form>
@endif

{{-- Vérifier le rôle admin --}}
@if(auth()->user()->isAdmin())
    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
@endif
```

## Validation des formulaires

### Dans le contrôleur

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string|min:10',
    ], [
        'title.required' => 'Le titre est obligatoire.',
        'content.required' => 'Le contenu est obligatoire.',
        'content.min' => 'Le contenu doit contenir au moins 10 caractères.',
    ]);

    $post = $request->user()->posts()->create($validated);

    return redirect()->route('posts.show', $post)
        ->with('success', 'Post créé avec succès!');
}
```

### Affichage des erreurs dans la vue

```blade
<form action="{{ route('posts.store') }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label for="title">Titre</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}">
        
        @error('title')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <label for="content">Contenu</label>
        <textarea name="content" id="content">{{ old('content') }}</textarea>
        
        @error('content')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>
    
    <button type="submit">Créer</button>
</form>
```

## Requêtes avancées

### Statistiques

```php
// Nombre de posts par utilisateur
$stats = User::withCount('posts')->get();

// Utilisateurs avec plus de 5 posts
$activeUsers = User::has('posts', '>', 5)->get();

// Posts les plus commentés
$popularPosts = Post::withCount('comments')
    ->orderBy('comments_count', 'desc')
    ->take(10)
    ->get();
```

### Recherche

```php
// Rechercher dans les posts
$posts = Post::where('title', 'like', '%Laravel%')
    ->orWhere('content', 'like', '%Laravel%')
    ->with(['user', 'tags'])
    ->get();

// Posts d'un utilisateur avec un tag spécifique
$posts = Post::where('user_id', 1)
    ->whereHas('tags', function ($query) {
        $query->where('name', 'Laravel');
    })
    ->get();
```

## Commandes Artisan utiles

```bash
# Créer un nouveau post via tinker
php artisan tinker
>>> $user = User::first();
>>> $user->posts()->create(['title' => 'Test', 'content' => 'Contenu test']);

# Afficher tous les posts avec leurs relations
>>> Post::with(['user', 'comments', 'tags'])->get();

# Compter les posts par utilisateur
>>> User::withCount('posts')->get()->pluck('posts_count', 'name');

# Supprimer tous les posts d'un utilisateur
>>> User::find(1)->posts()->delete();

# Détacher tous les tags d'un post
>>> Post::find(1)->tags()->detach();
```

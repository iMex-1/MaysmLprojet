# Projet Laravel - Gestion de Blog - COMPLET ✅

## Vue d'ensemble

Ce projet Laravel implémente une application complète de gestion de blog avec toutes les fonctionnalités demandées.

## ✅ Partie 2 : Relations Eloquent

### Migrations créées et complétées

1. **users** (`database/migrations/2026_04_18_090417_create_users_table.php`)
   - id, name, email, password, role (admin/user), timestamps

2. **posts** (`database/migrations/2026_04_18_090426_create_posts_table.php`)
   - id, user_id (FK), title, content, timestamps

3. **comments** (`database/migrations/2026_04_18_090435_create_comments_table.php`)
   - id, post_id (FK), user_id (FK), content, timestamps

4. **tags** (`database/migrations/2026_04_18_090444_create_tags_table.php`)
   - id, name, timestamps

5. **post_tag** (`database/migrations/2026_04_18_090449_create_post_tag_table.php`)
   - id, post_id (FK), tag_id (FK), timestamps

### Relations Eloquent implémentées

**User.php** (`app/Models/User.php`)
```php
public function posts() // One-to-Many
public function comments()
public function isAdmin() // Helper method
```

**Post.php** (`app/Models/Post.php`)
```php
public function user() // BelongsTo
public function comments() // One-to-Many
public function tags() // Many-to-Many
```

**Comment.php** (`app/Models/Comment.php`)
```php
public function post() // BelongsTo
public function user() // BelongsTo
```

**Tag.php** (`app/Models/Tag.php`)
```php
public function posts() // Many-to-Many
```

### Exemple d'utilisation
```php
// Afficher tous les posts d'un utilisateur avec leurs commentaires
$user = User::with(['posts.comments.user'])->find(1);
foreach ($user->posts as $post) {
    echo $post->title;
    foreach ($post->comments as $comment) {
        echo $comment->content;
    }
}
```

## ✅ Partie 3 : Authentification avec Breeze

### Installation
Le fichier `INSTALLATION.md` contient les instructions complètes :
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

### Modifications apportées

1. **Champ role ajouté** dans la migration users
   - Enum('admin', 'user') avec valeur par défaut 'user'

2. **Dashboard protégé** (`routes/web.php`)
   - Route `/dashboard` protégée par middleware `auth`
   - Affiche les posts de l'utilisateur connecté

3. **Méthode helper** dans User.php
   ```php
   public function isAdmin(): bool
   {
       return $this->role === 'admin';
   }
   ```

## ✅ Partie 4 : Autorisation (Gates & Policies)

### PostPolicy créée (`app/Policies/PostPolicy.php`)

**Méthodes implémentées :**
- `viewAny()` : Tous peuvent voir la liste
- `view()` : Tous peuvent voir un post
- `create()` : Tous les utilisateurs authentifiés
- `update()` : Seul l'auteur peut modifier
  ```php
  return $user->id === $post->user_id;
  ```
- `delete()` : Seul l'auteur peut supprimer

### Gate pour les admins (`app/Providers/AppServiceProvider.php`)

```php
Gate::define('delete-any-post', function ($user) {
    return $user->isAdmin();
});
```

### Utilisation dans les vues

**posts/index.blade.php** et **posts/show.blade.php** :
```blade
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}">Modifier</a>
@endcan

@if(Gate::allows('delete-any-post') || Gate::allows('delete', $post))
    <form action="{{ route('posts.destroy', $post) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Supprimer</button>
    </form>
@endif
```

## ✅ Partie 5 : Controllers & Views

### PostController (`app/Http/Controllers/PostController.php`)

**Toutes les méthodes RESTful implémentées :**

1. **index()** : Liste paginée des posts avec relations
   ```php
   $posts = Post::with(['user', 'comments'])->latest()->paginate(10);
   ```

2. **create()** : Formulaire de création (avec autorisation)

3. **store()** : Enregistrement avec validation
   ```php
   $validated = $request->validate([
       'title' => 'required|string|max:255',
       'content' => 'required|string',
   ]);
   ```

4. **show()** : Affichage avec commentaires et tags
   ```php
   $post->load(['user', 'comments.user', 'tags']);
   ```

5. **edit()** : Formulaire d'édition (avec autorisation)

6. **update()** : Mise à jour avec validation

7. **destroy()** : Suppression (avec vérification Gate/Policy)

### Vues Blade créées

**Layout principal** (`resources/views/layouts/app.blade.php`)
- Navigation avec liens conditionnels
- Affichage des messages flash
- Styles CSS intégrés

**Vues Posts** (`resources/views/posts/`)
- `index.blade.php` : Liste des posts avec pagination
- `create.blade.php` : Formulaire de création
- `edit.blade.php` : Formulaire d'édition
- `show.blade.php` : Affichage détaillé avec commentaires

**Vues Dashboard** (`resources/views/`)
- `dashboard.blade.php` : Dashboard utilisateur
- `admin/dashboard.blade.php` : Dashboard admin avec statistiques
- `admin/users.blade.php` : Gestion des utilisateurs

### Affichage des erreurs de validation

Toutes les vues de formulaire incluent :
```blade
@error('title')
    <div class="error">{{ $message }}</div>
@enderror
```

## ✅ Partie 6 : Routes

### Fichier routes/web.php organisé

**1. Routes publiques**
```php
Route::get('/', function () {
    return redirect()->route('posts.index');
});
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);
```

**2. Groupe protégé par auth**
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/posts/create', [PostController::class, 'create']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}/edit', [PostController::class, 'edit']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    Route::get('/dashboard', ...);
});
```

**3. Groupe réservé aux admins**
```php
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', ...);
        Route::get('/users', ...);
    });
```

### Middleware personnalisé (`app/Http/Middleware/AdminMiddleware.php`)

```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403, 'Accès refusé. Vous devez être administrateur.');
    }
    return $next($request);
}
```

**Enregistré dans** `bootstrap/app.php` :
```php
$middleware->alias([
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
]);
```

## Seeders

### DatabaseSeeder (`database/seeders/DatabaseSeeder.php`)
- Crée 2 utilisateurs de test (admin et user)
- Crée 3 utilisateurs supplémentaires
- Appelle PostSeeder

### PostSeeder (`database/seeders/PostSeeder.php`)
- Crée 4 tags
- Crée 3 posts par utilisateur
- Attache des tags aléatoires
- Crée des commentaires aléatoires

## Documentation

### Fichiers créés

1. **README.md** : Documentation complète du projet
2. **INSTALLATION.md** : Instructions d'installation détaillées
3. **EXEMPLES.md** : Exemples d'utilisation des relations et policies
4. **TESTS.md** : Guide de test complet
5. **PROJET_COMPLET.md** : Ce fichier récapitulatif

## Commandes pour démarrer

```bash
# 1. Installer Breeze
composer require laravel/breeze --dev
php artisan breeze:install blade

# 2. Installer et compiler
npm install
npm run build

# 3. Migrer et peupler
php artisan migrate:fresh --seed

# 4. Démarrer le serveur
php artisan serve
```

## Comptes de test

Après `php artisan db:seed` :
- **Admin** : admin@example.com / password
- **User** : user@example.com / password

## Vérification finale

### Checklist complète ✅

- [x] Migrations pour users, posts, comments, tags, post_tag
- [x] Relation One-to-Many : User → Posts
- [x] Relation One-to-Many : Post → Comments
- [x] Relation Many-to-Many : Posts ↔ Tags
- [x] Table pivot avec clés étrangères
- [x] Laravel Breeze (instructions d'installation)
- [x] Champ role (admin/user)
- [x] Dashboard protégé par auth
- [x] PostPolicy créée
- [x] Utilisateur modifie uniquement ses posts
- [x] Gate delete-any-post pour admins
- [x] @can dans Blade
- [x] PostController avec toutes les méthodes RESTful
- [x] Vues Blade (index, create, edit, show)
- [x] Affichage des erreurs de validation
- [x] Routes protégées par auth
- [x] Routes RESTful pour posts
- [x] Routes admin avec prefix
- [x] Middleware personnalisé AdminMiddleware

## Structure finale

```
app/
├── Http/
│   ├── Controllers/
│   │   └── PostController.php ✅
│   └── Middleware/
│       └── AdminMiddleware.php ✅
├── Models/
│   ├── User.php ✅
│   ├── Post.php ✅
│   ├── Comment.php ✅
│   └── Tag.php ✅
├── Policies/
│   └── PostPolicy.php ✅
└── Providers/
    └── AppServiceProvider.php ✅

database/
├── migrations/ ✅ (5 migrations complètes)
└── seeders/ ✅ (DatabaseSeeder + PostSeeder)

resources/views/
├── layouts/app.blade.php ✅
├── posts/ ✅ (4 vues)
├── admin/ ✅ (2 vues)
└── dashboard.blade.php ✅

routes/
└── web.php ✅ (Routes organisées)

Documentation/
├── README.md ✅
├── INSTALLATION.md ✅
├── EXEMPLES.md ✅
├── TESTS.md ✅
└── PROJET_COMPLET.md ✅
```

## Conclusion

Tous les éléments demandés ont été implémentés avec succès. Le projet est prêt à être testé après l'installation de Laravel Breeze et l'exécution des migrations.

Pour toute question ou problème, consultez les fichiers de documentation ou le fichier TESTS.md pour le guide de test complet.

# Guide de Test de l'Application

## Prérequis

Assurez-vous d'avoir :
1. Installé Laravel Breeze : `composer require laravel/breeze --dev && php artisan breeze:install blade`
2. Exécuté les migrations : `php artisan migrate`
3. Peuplé la base de données : `php artisan db:seed`
4. Compilé les assets : `npm install && npm run build`

## Tests Manuels

### 1. Test des Relations Eloquent

Ouvrez `php artisan tinker` et testez :

```php
// Test relation User -> Posts
$user = User::first();
$user->posts; // Doit retourner une collection de posts

// Test relation Post -> Comments
$post = Post::first();
$post->comments; // Doit retourner une collection de commentaires

// Test relation Post -> Tags (Many-to-Many)
$post->tags; // Doit retourner une collection de tags

// Test relation inverse
$comment = Comment::first();
$comment->post; // Doit retourner le post
$comment->user; // Doit retourner l'utilisateur

// Afficher tous les posts d'un utilisateur avec leurs commentaires
$user = User::with(['posts.comments.user'])->first();
foreach ($user->posts as $post) {
    echo "Post: {$post->title}\n";
    echo "Commentaires: {$post->comments->count()}\n";
}
```

### 2. Test de l'Authentification

1. Démarrez le serveur : `php artisan serve`
2. Visitez http://localhost:8000
3. Cliquez sur "Inscription" et créez un compte
4. Vérifiez que vous êtes redirigé vers le dashboard
5. Déconnectez-vous et reconnectez-vous

**Comptes de test :**
- Admin : admin@example.com / password
- User : user@example.com / password

### 3. Test des Autorisations (Policies)

#### Test : Modifier son propre post
1. Connectez-vous avec user@example.com
2. Créez un nouveau post
3. Vérifiez que le bouton "Modifier" apparaît sur votre post
4. Cliquez sur "Modifier" et modifiez le post
5. ✅ Succès : Le post est modifié

#### Test : Modifier le post d'un autre utilisateur
1. Connectez-vous avec user@example.com
2. Trouvez un post créé par "Admin"
3. Vérifiez que le bouton "Modifier" n'apparaît PAS
4. Essayez d'accéder directement à l'URL d'édition
5. ✅ Succès : Erreur 403 (Accès refusé)

### 4. Test du Gate Admin

#### Test : Supprimer n'importe quel post (Admin)
1. Connectez-vous avec admin@example.com
2. Visitez la liste des posts
3. Vérifiez que le bouton "Supprimer" apparaît sur TOUS les posts
4. Supprimez un post créé par un autre utilisateur
5. ✅ Succès : Le post est supprimé

#### Test : Supprimer uniquement ses posts (User)
1. Connectez-vous avec user@example.com
2. Visitez la liste des posts
3. Vérifiez que le bouton "Supprimer" apparaît uniquement sur VOS posts
4. ✅ Succès : Vous ne pouvez pas supprimer les posts des autres

### 5. Test des Controllers & Views

#### Test : Liste des posts (index)
1. Visitez http://localhost:8000/posts
2. Vérifiez que tous les posts sont affichés
3. Vérifiez la pagination si plus de 10 posts
4. ✅ Succès : Liste affichée correctement

#### Test : Créer un post (create/store)
1. Connectez-vous
2. Cliquez sur "Créer un nouveau post"
3. Remplissez le formulaire
4. Soumettez
5. ✅ Succès : Post créé et redirection vers la page du post

#### Test : Validation des erreurs
1. Essayez de créer un post sans titre
2. Vérifiez que l'erreur "Le titre est obligatoire" s'affiche
3. Essayez avec un titre mais sans contenu
4. Vérifiez que l'erreur "Le contenu est obligatoire" s'affiche
5. ✅ Succès : Erreurs affichées correctement

#### Test : Afficher un post (show)
1. Cliquez sur un post
2. Vérifiez que le titre, contenu, auteur et date sont affichés
3. Vérifiez que les commentaires sont affichés
4. Vérifiez que les tags sont affichés
5. ✅ Succès : Toutes les informations sont visibles

#### Test : Modifier un post (edit/update)
1. Connectez-vous et créez un post
2. Cliquez sur "Modifier"
3. Modifiez le titre et le contenu
4. Soumettez
5. ✅ Succès : Post mis à jour

#### Test : Supprimer un post (destroy)
1. Connectez-vous et créez un post
2. Cliquez sur "Supprimer"
3. Confirmez la suppression
4. ✅ Succès : Post supprimé et redirection vers la liste

### 6. Test des Routes

#### Routes publiques (sans authentification)
```bash
# Tester avec curl ou le navigateur
curl http://localhost:8000/posts
curl http://localhost:8000/posts/1
```
✅ Succès : Accessible sans connexion

#### Routes protégées (avec authentification)
1. Déconnectez-vous
2. Essayez d'accéder à http://localhost:8000/posts/create
3. ✅ Succès : Redirection vers la page de connexion

#### Routes admin
1. Connectez-vous avec user@example.com
2. Essayez d'accéder à http://localhost:8000/admin/dashboard
3. ✅ Succès : Erreur 403 (Accès refusé)
4. Connectez-vous avec admin@example.com
5. Accédez à http://localhost:8000/admin/dashboard
6. ✅ Succès : Dashboard admin affiché

### 7. Test du Middleware Personnalisé

#### Test : AdminMiddleware
1. Connectez-vous avec user@example.com (rôle: user)
2. Essayez d'accéder à /admin/dashboard
3. ✅ Succès : Erreur 403 avec message "Accès refusé. Vous devez être administrateur."

4. Connectez-vous avec admin@example.com (rôle: admin)
5. Accédez à /admin/dashboard
6. ✅ Succès : Accès autorisé

### 8. Test du Dashboard

#### Dashboard utilisateur
1. Connectez-vous avec n'importe quel compte
2. Visitez http://localhost:8000/dashboard
3. Vérifiez que vos posts sont affichés
4. Vérifiez que vous pouvez modifier/supprimer vos posts
5. ✅ Succès : Dashboard fonctionnel

#### Dashboard admin
1. Connectez-vous avec admin@example.com
2. Visitez http://localhost:8000/admin/dashboard
3. Vérifiez les statistiques (nombre d'utilisateurs, posts, commentaires)
4. Vérifiez la liste des posts récents
5. Cliquez sur "Gérer les utilisateurs"
6. ✅ Succès : Toutes les fonctionnalités admin fonctionnent

## Tests Automatisés (Optionnel)

Créez des tests PHPUnit pour automatiser ces vérifications :

```bash
php artisan make:test PostTest
php artisan make:test AuthorizationTest
```

Exemple de test :

```php
public function test_user_can_create_post()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->post('/posts', [
        'title' => 'Test Post',
        'content' => 'Test Content',
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'user_id' => $user->id,
    ]);
}

public function test_user_cannot_edit_others_post()
{
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user1->id]);
    
    $response = $this->actingAs($user2)->get("/posts/{$post->id}/edit");
    
    $response->assertStatus(403);
}
```

Exécutez les tests :
```bash
php artisan test
```

## Checklist Complète

- [ ] Relations Eloquent fonctionnent (User->Posts, Post->Comments, Post->Tags)
- [ ] Authentification avec Breeze installée
- [ ] Champ role (admin/user) dans users
- [ ] Dashboard protégé par auth
- [ ] PostPolicy créée et fonctionnelle
- [ ] Utilisateur peut modifier uniquement ses posts
- [ ] Gate delete-any-post pour les admins
- [ ] @can utilisé dans les vues Blade
- [ ] PostController avec toutes les méthodes RESTful
- [ ] Vues Blade créées (index, create, edit, show)
- [ ] Erreurs de validation affichées
- [ ] Routes protégées par auth
- [ ] Routes RESTful pour posts
- [ ] Routes admin avec prefix /admin
- [ ] Middleware AdminMiddleware créé et fonctionnel

## Résolution des Problèmes

### Erreur : Class 'Breeze' not found
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

### Erreur : Table 'users' doesn't exist
```bash
php artisan migrate:fresh --seed
```

### Erreur : npm not found
```bash
# Installez Node.js depuis https://nodejs.org/
npm install
npm run build
```

### Erreur 403 sur toutes les routes
Vérifiez que vous êtes bien connecté et que les policies sont correctement enregistrées.

### Les boutons "Modifier" n'apparaissent pas
Vérifiez que la PostPolicy est bien enregistrée et que vous utilisez `@can('update', $post)` dans les vues.

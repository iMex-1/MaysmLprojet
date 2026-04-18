# Application de Gestion de Blog Laravel

Application complète de gestion de blog développée avec Laravel, incluant l'authentification, les autorisations et un système de gestion de contenu.

## Fonctionnalités

### Partie 2 : Relations Eloquent ✅
- Migrations complètes pour users, posts, comments, tags et post_tag
- Relation One-to-Many : User → Posts
- Relation One-to-Many : Post → Comments
- Relation Many-to-Many : Posts ↔ Tags
- Table pivot post_tag avec clés étrangères

### Partie 3 : Authentification avec Breeze ✅
- Laravel Breeze installable (voir INSTALLATION.md)
- Champ `role` (admin/user) dans la table users
- Dashboard protégé par authentification
- Système de connexion/inscription complet

### Partie 4 : Autorisation (Gates & Policies) ✅
- PostPolicy créée avec les méthodes d'autorisation
- Un utilisateur peut modifier uniquement ses propres posts
- Gate `delete-any-post` permettant aux admins de supprimer n'importe quel post
- Utilisation de `@can` dans les vues Blade pour afficher les boutons conditionnellement

### Partie 5 : Controllers & Views ✅
- PostController avec toutes les méthodes RESTful :
  - index() : Liste des posts avec pagination
  - create() : Formulaire de création
  - store() : Enregistrement avec validation
  - show() : Affichage d'un post avec commentaires
  - edit() : Formulaire d'édition
  - update() : Mise à jour avec validation
  - destroy() : Suppression
- Vues Blade complètes :
  - Layout principal (layouts/app.blade.php)
  - Liste des posts (posts/index.blade.php)
  - Formulaire de création (posts/create.blade.php)
  - Formulaire d'édition (posts/edit.blade.php)
  - Affichage d'un post (posts/show.blade.php)
  - Dashboard utilisateur (dashboard.blade.php)
  - Dashboard admin (admin/dashboard.blade.php)
- Affichage des erreurs de validation dans toutes les vues

### Partie 6 : Routes ✅
- Groupe de routes protégées par `auth` middleware
- Routes RESTful pour les posts
- Groupe de routes réservé aux admins avec prefix `/admin`
- Middleware personnalisé `AdminMiddleware` pour vérifier le rôle admin
- Organisation claire des routes par niveau d'accès

## Structure du Projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── PostController.php (RESTful controller)
│   │   └── UserController.php
│   └── Middleware/
│       └── AdminMiddleware.php (Middleware personnalisé)
├── Models/
│   ├── User.php (avec relations et méthode isAdmin())
│   ├── Post.php (avec relations)
│   ├── Comment.php (avec relations)
│   └── Tag.php (avec relations)
└── Policies/
    └── PostPolicy.php (Autorisations pour les posts)

database/
├── migrations/
│   ├── 2026_04_18_090417_create_users_table.php
│   ├── 2026_04_18_090426_create_posts_table.php
│   ├── 2026_04_18_090435_create_comments_table.php
│   ├── 2026_04_18_090444_create_tags_table.php
│   └── 2026_04_18_090449_create_post_tag_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── PostSeeder.php

resources/views/
├── layouts/
│   └── app.blade.php
├── posts/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── admin/
│   ├── dashboard.blade.php
│   └── users.blade.php
└── dashboard.blade.php

routes/
└── web.php (Routes organisées par niveau d'accès)
```

## Installation

Consultez le fichier `INSTALLATION.md` pour les instructions détaillées d'installation.

### Résumé rapide

```bash
# 1. Installer les dépendances
composer install

# 2. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 3. Installer Breeze
composer require laravel/breeze --dev
php artisan breeze:install blade

# 4. Installer NPM et compiler
npm install
npm run build

# 5. Configurer la base de données
php artisan migrate

# 6. Peupler la base de données (optionnel)
php artisan db:seed

# 7. Démarrer le serveur
php artisan serve
```

## Comptes de Test

Après avoir exécuté `php artisan db:seed` :

- **Admin** : admin@example.com / password
- **User** : user@example.com / password

## Routes Principales

### Routes Publiques
- `GET /` : Redirection vers la liste des posts
- `GET /posts` : Liste des posts
- `GET /posts/{post}` : Affichage d'un post

### Routes Authentifiées
- `GET /dashboard` : Dashboard utilisateur
- `GET /posts/create` : Formulaire de création
- `POST /posts` : Enregistrement d'un post
- `GET /posts/{post}/edit` : Formulaire d'édition
- `PUT /posts/{post}` : Mise à jour d'un post
- `DELETE /posts/{post}` : Suppression d'un post

### Routes Admin
- `GET /admin/dashboard` : Dashboard administrateur
- `GET /admin/users` : Gestion des utilisateurs

## Autorisations

### Policies (PostPolicy)
- `viewAny` : Tous les utilisateurs peuvent voir la liste
- `view` : Tous les utilisateurs peuvent voir un post
- `create` : Tous les utilisateurs authentifiés peuvent créer
- `update` : Seul l'auteur peut modifier son post
- `delete` : Seul l'auteur peut supprimer son post

### Gates
- `delete-any-post` : Seuls les admins peuvent supprimer n'importe quel post

## Utilisation dans les Vues

```blade
{{-- Vérifier si l'utilisateur peut modifier --}}
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}">Modifier</a>
@endcan

{{-- Vérifier le gate admin --}}
@if(Gate::allows('delete-any-post'))
    <button>Supprimer (Admin)</button>
@endif
```

## Technologies Utilisées

- Laravel 13
- PHP 8.3
- Laravel Breeze (Authentification)
- Blade (Templates)
- SQLite (Base de données)
- Vite (Build tool)

## Licence

MIT

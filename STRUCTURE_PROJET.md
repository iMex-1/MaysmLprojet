# Structure du Projet Laravel - Blog

## 📁 Arborescence complète

```
MaysmLprojet/
│
├── 📄 README.md                          ✅ Documentation principale
├── 📄 INSTALLATION.md                    ✅ Guide d'installation
├── 📄 DEMARRAGE_RAPIDE.md               ✅ Démarrage en 5 minutes
├── 📄 EXEMPLES.md                        ✅ Exemples de code
├── 📄 TESTS.md                           ✅ Guide de test
├── 📄 PROJET_COMPLET.md                 ✅ Récapitulatif complet
├── 📄 STRUCTURE_PROJET.md               ✅ Ce fichier
│
├── 📂 app/
│   ├── 📂 Http/
│   │   ├── 📂 Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── PostController.php       ✅ CRUD complet des posts
│   │   │   └── UserController.php
│   │   │
│   │   └── 📂 Middleware/
│   │       └── AdminMiddleware.php      ✅ Middleware personnalisé
│   │
│   ├── 📂 Models/
│   │   ├── User.php                     ✅ Relations + isAdmin()
│   │   ├── Post.php                     ✅ Relations complètes
│   │   ├── Comment.php                  ✅ Relations complètes
│   │   └── Tag.php                      ✅ Relations Many-to-Many
│   │
│   ├── 📂 Policies/
│   │   └── PostPolicy.php               ✅ Autorisations
│   │
│   └── 📂 Providers/
│       └── AppServiceProvider.php       ✅ Gate delete-any-post
│
├── 📂 bootstrap/
│   └── app.php                          ✅ Middleware enregistré
│
├── 📂 config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── ... (autres configs)
│
├── 📂 database/
│   ├── database.sqlite                  📊 Base de données
│   │
│   ├── 📂 migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_04_18_090417_create_users_table.php      ✅ Users avec role
│   │   ├── 2026_04_18_090426_create_posts_table.php      ✅ Posts avec FK
│   │   ├── 2026_04_18_090435_create_comments_table.php   ✅ Comments avec FK
│   │   ├── 2026_04_18_090444_create_tags_table.php       ✅ Tags
│   │   └── 2026_04_18_090449_create_post_tag_table.php   ✅ Table pivot
│   │
│   ├── 📂 seeders/
│   │   ├── DatabaseSeeder.php           ✅ Seeder principal
│   │   └── PostSeeder.php               ✅ Seeder posts/tags/comments
│   │
│   └── 📂 factories/
│       └── UserFactory.php
│
├── 📂 public/
│   ├── index.php
│   └── ... (assets publics)
│
├── 📂 resources/
│   ├── 📂 views/
│   │   ├── 📂 layouts/
│   │   │   └── app.blade.php            ✅ Layout principal
│   │   │
│   │   ├── 📂 posts/
│   │   │   ├── index.blade.php          ✅ Liste des posts
│   │   │   ├── create.blade.php         ✅ Formulaire création
│   │   │   ├── edit.blade.php           ✅ Formulaire édition
│   │   │   └── show.blade.php           ✅ Affichage post
│   │   │
│   │   ├── 📂 admin/
│   │   │   ├── dashboard.blade.php      ✅ Dashboard admin
│   │   │   └── users.blade.php          ✅ Gestion utilisateurs
│   │   │
│   │   ├── dashboard.blade.php          ✅ Dashboard utilisateur
│   │   └── welcome.blade.php
│   │
│   ├── 📂 css/
│   │   └── app.css
│   │
│   └── 📂 js/
│       └── app.js
│
├── 📂 routes/
│   ├── web.php                          ✅ Routes organisées
│   └── console.php
│
├── 📂 tests/
│   ├── 📂 Feature/
│   │   └── ExampleTest.php
│   └── 📂 Unit/
│       └── ExampleTest.php
│
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
└── vite.config.js
```

## 🎯 Fichiers clés créés/modifiés

### Modèles (app/Models/)
| Fichier | Description | Relations |
|---------|-------------|-----------|
| User.php | Utilisateur avec rôle | hasMany(Post), hasMany(Comment) |
| Post.php | Article de blog | belongsTo(User), hasMany(Comment), belongsToMany(Tag) |
| Comment.php | Commentaire | belongsTo(Post), belongsTo(User) |
| Tag.php | Étiquette | belongsToMany(Post) |

### Contrôleurs (app/Http/Controllers/)
| Fichier | Méthodes | Description |
|---------|----------|-------------|
| PostController.php | index, create, store, show, edit, update, destroy | CRUD complet avec autorisations |

### Middleware (app/Http/Middleware/)
| Fichier | Description |
|---------|-------------|
| AdminMiddleware.php | Vérifie si l'utilisateur est admin |

### Policies (app/Policies/)
| Fichier | Méthodes | Description |
|---------|----------|-------------|
| PostPolicy.php | viewAny, view, create, update, delete | Autorisations pour les posts |

### Migrations (database/migrations/)
| Fichier | Table | Colonnes principales |
|---------|-------|---------------------|
| create_users_table.php | users | name, email, password, role |
| create_posts_table.php | posts | user_id, title, content |
| create_comments_table.php | comments | post_id, user_id, content |
| create_tags_table.php | tags | name |
| create_post_tag_table.php | post_tag | post_id, tag_id |

### Vues (resources/views/)
| Fichier | Description | Fonctionnalités |
|---------|-------------|-----------------|
| layouts/app.blade.php | Layout principal | Navigation, messages flash |
| posts/index.blade.php | Liste des posts | Pagination, @can |
| posts/create.blade.php | Création | Validation, @error |
| posts/edit.blade.php | Édition | Validation, @error |
| posts/show.blade.php | Affichage | Commentaires, tags, @can |
| dashboard.blade.php | Dashboard user | Liste des posts de l'utilisateur |
| admin/dashboard.blade.php | Dashboard admin | Statistiques, posts récents |
| admin/users.blade.php | Gestion users | Liste avec rôles |

### Routes (routes/web.php)
| Type | Routes | Middleware |
|------|--------|------------|
| Publiques | /, /posts, /posts/{post} | - |
| Authentifiées | /dashboard, /posts/create, etc. | auth |
| Admin | /admin/dashboard, /admin/users | auth, admin |

## 📊 Base de données

### Tables créées
1. **users** - Utilisateurs avec rôle (admin/user)
2. **posts** - Articles de blog
3. **comments** - Commentaires sur les posts
4. **tags** - Étiquettes pour catégoriser
5. **post_tag** - Table pivot Many-to-Many

### Relations
```
User (1) ──────> (N) Post
User (1) ──────> (N) Comment
Post (1) ──────> (N) Comment
Post (N) <─────> (N) Tag (via post_tag)
```

## 🔐 Système d'autorisation

### Policies
- **PostPolicy** : Contrôle qui peut voir/créer/modifier/supprimer les posts
  - Seul l'auteur peut modifier/supprimer son post

### Gates
- **delete-any-post** : Permet aux admins de supprimer n'importe quel post

### Middleware
- **auth** : Vérifie que l'utilisateur est connecté
- **admin** : Vérifie que l'utilisateur a le rôle admin

## 📝 Documentation

| Fichier | Contenu |
|---------|---------|
| README.md | Documentation complète du projet |
| INSTALLATION.md | Instructions d'installation détaillées |
| DEMARRAGE_RAPIDE.md | Guide de démarrage en 5 minutes |
| EXEMPLES.md | Exemples de code et d'utilisation |
| TESTS.md | Guide de test complet |
| PROJET_COMPLET.md | Récapitulatif de toutes les fonctionnalités |
| STRUCTURE_PROJET.md | Ce fichier - Structure du projet |

## ✅ Checklist de vérification

### Partie 2 : Relations Eloquent
- [x] Migration users complète
- [x] Migration posts complète
- [x] Migration comments complète
- [x] Migration tags complète
- [x] Migration post_tag (pivot) complète
- [x] Relation User → Posts (One-to-Many)
- [x] Relation Post → Comments (One-to-Many)
- [x] Relation Post ↔ Tags (Many-to-Many)
- [x] Modèles avec relations définies

### Partie 3 : Authentification
- [x] Instructions d'installation Breeze
- [x] Champ role dans users
- [x] Dashboard protégé par auth
- [x] Méthode isAdmin() dans User

### Partie 4 : Autorisation
- [x] PostPolicy créée
- [x] Méthode update() vérifie l'auteur
- [x] Gate delete-any-post pour admins
- [x] @can utilisé dans les vues

### Partie 5 : Controllers & Views
- [x] PostController avec 7 méthodes RESTful
- [x] Vue index avec pagination
- [x] Vue create avec formulaire
- [x] Vue edit avec formulaire
- [x] Vue show avec commentaires
- [x] Validation des formulaires
- [x] Affichage des erreurs @error

### Partie 6 : Routes
- [x] Routes publiques
- [x] Groupe auth pour routes protégées
- [x] Routes RESTful pour posts
- [x] Groupe admin avec prefix
- [x] AdminMiddleware créé et enregistré

## 🚀 Prêt à démarrer !

Suivez le fichier **DEMARRAGE_RAPIDE.md** pour installer et tester l'application en 5 minutes.

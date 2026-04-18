# 🛠️ Commandes Utiles

## 📦 Installation

### Installation initiale
```bash
# Installer les dépendances PHP
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Installer Laravel Breeze
composer require laravel/breeze --dev
php artisan breeze:install blade

# Installer les dépendances NPM
npm install

# Compiler les assets
npm run build
```

### Base de données
```bash
# Créer la base de données SQLite
touch database/database.sqlite

# Exécuter les migrations
php artisan migrate

# Exécuter les migrations avec seed
php artisan migrate --seed

# Réinitialiser la base de données
php artisan migrate:fresh --seed
```

---

## 🚀 Démarrage

### Serveur de développement
```bash
# Démarrer le serveur Laravel
php artisan serve

# Démarrer sur un port spécifique
php artisan serve --port=8001

# Compiler les assets en mode watch
npm run dev
```

### Accès à l'application
```
http://localhost:8000
```

---

## 🗄️ Base de Données

### Migrations
```bash
# Créer une nouvelle migration
php artisan make:migration create_table_name

# Exécuter les migrations
php artisan migrate

# Rollback dernière migration
php artisan migrate:rollback

# Rollback toutes les migrations
php artisan migrate:reset

# Réinitialiser et re-migrer
php artisan migrate:fresh

# Réinitialiser avec seed
php artisan migrate:fresh --seed

# Voir le statut des migrations
php artisan migrate:status
```

### Seeders
```bash
# Créer un seeder
php artisan make:seeder NomSeeder

# Exécuter tous les seeders
php artisan db:seed

# Exécuter un seeder spécifique
php artisan db:seed --class=PostSeeder
```

### Tinker (Console interactive)
```bash
# Ouvrir tinker
php artisan tinker

# Dans tinker - Exemples
>>> User::all()
>>> User::find(1)
>>> Post::with('comments')->get()
>>> User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => bcrypt('password'), 'role' => 'user'])
```

---

## 🏗️ Génération de Code

### Modèles
```bash
# Créer un modèle
php artisan make:model NomModele

# Créer un modèle avec migration
php artisan make:model NomModele -m

# Créer un modèle avec migration, factory et seeder
php artisan make:model NomModele -mfs
```

### Contrôleurs
```bash
# Créer un contrôleur
php artisan make:controller NomController

# Créer un contrôleur RESTful
php artisan make:controller NomController --resource

# Créer un contrôleur avec modèle
php artisan make:controller NomController --model=NomModele
```

### Middleware
```bash
# Créer un middleware
php artisan make:middleware NomMiddleware
```

### Policies
```bash
# Créer une policy
php artisan make:policy NomPolicy

# Créer une policy avec modèle
php artisan make:policy NomPolicy --model=NomModele
```

### Requests (Validation)
```bash
# Créer une request
php artisan make:request NomRequest
```

---

## 🧪 Tests

### Créer des tests
```bash
# Créer un test Feature
php artisan make:test NomTest

# Créer un test Unit
php artisan make:test NomTest --unit
```

### Exécuter les tests
```bash
# Exécuter tous les tests
php artisan test

# Exécuter un test spécifique
php artisan test --filter NomTest

# Exécuter avec couverture
php artisan test --coverage
```

---

## 🔧 Maintenance

### Cache
```bash
# Vider tous les caches
php artisan cache:clear

# Vider le cache de configuration
php artisan config:clear

# Vider le cache de routes
php artisan route:clear

# Vider le cache de vues
php artisan view:clear

# Mettre en cache la configuration
php artisan config:cache

# Mettre en cache les routes
php artisan route:cache
```

### Optimisation
```bash
# Optimiser l'application
php artisan optimize

# Vider l'optimisation
php artisan optimize:clear
```

---

## 📋 Informations

### Routes
```bash
# Lister toutes les routes
php artisan route:list

# Lister les routes avec middleware
php artisan route:list --columns=uri,name,middleware

# Filtrer les routes
php artisan route:list --path=posts
```

### Autres
```bash
# Lister toutes les commandes
php artisan list

# Aide sur une commande
php artisan help migrate

# Voir la version de Laravel
php artisan --version
```

---

## 🎨 Assets (Vite)

### Développement
```bash
# Compiler une fois
npm run build

# Compiler en mode watch
npm run dev

# Installer les dépendances
npm install

# Mettre à jour les dépendances
npm update
```

---

## 🔐 Authentification (Breeze)

### Installation
```bash
# Installer Breeze
composer require laravel/breeze --dev

# Installer avec Blade
php artisan breeze:install blade

# Installer avec React
php artisan breeze:install react

# Installer avec Vue
php artisan breeze:install vue
```

---

## 📊 Commandes Personnalisées pour ce Projet

### Créer un utilisateur admin
```bash
php artisan tinker
```
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
```

### Créer un utilisateur normal
```bash
php artisan tinker
```
```php
\App\Models\User::create([
    'name' => 'User',
    'email' => 'user@example.com',
    'password' => bcrypt('password'),
    'role' => 'user'
]);
```

### Afficher tous les posts avec relations
```bash
php artisan tinker
```
```php
\App\Models\Post::with(['user', 'comments.user', 'tags'])->get();
```

### Créer un post avec tags
```bash
php artisan tinker
```
```php
$user = \App\Models\User::first();
$post = $user->posts()->create([
    'title' => 'Mon post',
    'content' => 'Contenu du post'
]);
$tag = \App\Models\Tag::firstOrCreate(['name' => 'Laravel']);
$post->tags()->attach($tag->id);
```

### Créer un commentaire
```bash
php artisan tinker
```
```php
$post = \App\Models\Post::first();
$user = \App\Models\User::first();
$post->comments()->create([
    'user_id' => $user->id,
    'content' => 'Super article !'
]);
```

### Statistiques
```bash
php artisan tinker
```
```php
// Nombre de posts par utilisateur
\App\Models\User::withCount('posts')->get()->pluck('posts_count', 'name');

// Posts les plus commentés
\App\Models\Post::withCount('comments')->orderBy('comments_count', 'desc')->take(5)->get();

// Utilisateurs les plus actifs
\App\Models\User::has('posts', '>', 2)->get();
```

---

## 🐛 Débogage

### Logs
```bash
# Voir les logs en temps réel
tail -f storage/logs/laravel.log

# Vider les logs
> storage/logs/laravel.log
```

### Mode Debug
```bash
# Dans .env
APP_DEBUG=true
```

### Afficher les requêtes SQL
```php
// Dans tinker ou dans le code
DB::enableQueryLog();
// ... vos requêtes ...
dd(DB::getQueryLog());
```

---

## 🔄 Git

### Commandes de base
```bash
# Initialiser un dépôt
git init

# Ajouter tous les fichiers
git add .

# Commit
git commit -m "Message"

# Voir le statut
git status

# Voir l'historique
git log --oneline
```

---

## 📦 Composer

### Gestion des dépendances
```bash
# Installer les dépendances
composer install

# Mettre à jour les dépendances
composer update

# Ajouter une dépendance
composer require vendor/package

# Ajouter une dépendance de dev
composer require --dev vendor/package

# Supprimer une dépendance
composer remove vendor/package

# Dump autoload
composer dump-autoload
```

---

## 🎯 Workflow Complet

### Démarrage d'un nouveau jour
```bash
# 1. Mettre à jour les dépendances
composer install
npm install

# 2. Démarrer les serveurs
php artisan serve &
npm run dev &

# 3. Ouvrir le navigateur
# http://localhost:8000
```

### Avant de committer
```bash
# 1. Vérifier le code
php artisan test

# 2. Vider les caches
php artisan cache:clear
php artisan config:clear

# 3. Optimiser
php artisan optimize

# 4. Commit
git add .
git commit -m "Description des changements"
```

### Déploiement
```bash
# 1. Mettre en production
composer install --optimize-autoloader --no-dev

# 2. Optimiser
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Migrer
php artisan migrate --force

# 4. Compiler les assets
npm run build
```

---

## 🆘 Commandes de Secours

### Réinitialisation complète
```bash
# Attention : Supprime toutes les données !
php artisan migrate:fresh --seed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
npm run build
```

### Problèmes de permissions (Linux/Mac)
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

### Réinstallation complète
```bash
# Supprimer les dépendances
rm -rf vendor node_modules

# Réinstaller
composer install
npm install
npm run build

# Reconfigurer
php artisan key:generate
php artisan migrate:fresh --seed
```

---

## 📚 Ressources

### Documentation Laravel
- https://laravel.com/docs
- https://laravel.com/docs/eloquent
- https://laravel.com/docs/blade

### Documentation Breeze
- https://laravel.com/docs/starter-kits#laravel-breeze

### Communauté
- https://laracasts.com
- https://laravel.io
- https://stackoverflow.com/questions/tagged/laravel

---

**💡 Astuce :** Ajoutez ces commandes à vos favoris pour un accès rapide !

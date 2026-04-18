# 🚀 Démarrage Rapide

## Installation en 5 minutes

### 1. Installer Laravel Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

Choisissez les options suivantes quand demandé :
- Testing framework: PHPUnit (ou appuyez sur Entrée)
- Dark mode: no (ou appuyez sur Entrée)

### 2. Installer les dépendances NPM
```bash
npm install
```

### 3. Compiler les assets
```bash
npm run build
```

### 4. Configurer la base de données
Le projet utilise SQLite par défaut. Vérifiez que le fichier `database/database.sqlite` existe.

Si ce n'est pas le cas :
```bash
touch database/database.sqlite
```

### 5. Exécuter les migrations
```bash
php artisan migrate:fresh --seed
```

Cette commande va :
- Créer toutes les tables (users, posts, comments, tags, post_tag)
- Créer 2 utilisateurs de test (admin et user)
- Créer 3 utilisateurs supplémentaires
- Créer des posts, commentaires et tags de démonstration

### 6. Démarrer le serveur
```bash
php artisan serve
```

### 7. Accéder à l'application
Ouvrez votre navigateur et visitez : **http://localhost:8000**

## 🔐 Comptes de test

Utilisez ces comptes pour vous connecter :

### Compte Administrateur
- **Email** : admin@example.com
- **Mot de passe** : password
- **Accès** : Toutes les fonctionnalités + Dashboard admin

### Compte Utilisateur
- **Email** : user@example.com
- **Mot de passe** : password
- **Accès** : Fonctionnalités utilisateur standard

## 📋 Que tester ?

### En tant qu'utilisateur normal (user@example.com)
1. ✅ Créer un nouveau post
2. ✅ Modifier vos propres posts
3. ✅ Supprimer vos propres posts
4. ❌ Modifier les posts des autres (doit être refusé)
5. ❌ Accéder au dashboard admin (doit être refusé)

### En tant qu'administrateur (admin@example.com)
1. ✅ Créer un nouveau post
2. ✅ Modifier vos propres posts
3. ✅ Supprimer N'IMPORTE QUEL post (même ceux des autres)
4. ✅ Accéder au dashboard admin (/admin/dashboard)
5. ✅ Voir la liste des utilisateurs (/admin/users)

## 🎯 Fonctionnalités principales

### Pages publiques (sans connexion)
- `/` ou `/posts` : Liste de tous les posts
- `/posts/{id}` : Voir un post avec ses commentaires

### Pages authentifiées
- `/dashboard` : Votre dashboard personnel
- `/posts/create` : Créer un nouveau post
- `/posts/{id}/edit` : Modifier votre post

### Pages admin (admin uniquement)
- `/admin/dashboard` : Dashboard administrateur avec statistiques
- `/admin/users` : Liste des utilisateurs

## 🔧 Commandes utiles

### Réinitialiser la base de données
```bash
php artisan migrate:fresh --seed
```

### Créer un nouvel utilisateur admin
```bash
php artisan tinker
```
Puis dans tinker :
```php
\App\Models\User::create([
    'name' => 'Nouvel Admin',
    'email' => 'nouvel.admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
```

### Voir tous les posts avec leurs relations
```bash
php artisan tinker
```
Puis :
```php
\App\Models\Post::with(['user', 'comments', 'tags'])->get();
```

### Compiler les assets en mode développement (watch)
```bash
npm run dev
```

## 📚 Documentation complète

Pour plus de détails, consultez :
- **README.md** : Documentation complète du projet
- **INSTALLATION.md** : Instructions d'installation détaillées
- **EXEMPLES.md** : Exemples de code et d'utilisation
- **TESTS.md** : Guide de test complet
- **PROJET_COMPLET.md** : Récapitulatif de toutes les fonctionnalités

## ❓ Problèmes courants

### Erreur : "Class 'Breeze' not found"
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

### Erreur : "npm: command not found"
Installez Node.js depuis https://nodejs.org/

### Erreur : "SQLSTATE[HY000]: General error: 1 no such table"
```bash
php artisan migrate:fresh --seed
```

### Les styles ne s'affichent pas
```bash
npm install
npm run build
```

### Port 8000 déjà utilisé
```bash
php artisan serve --port=8001
```

## 🎉 C'est tout !

Votre application de blog Laravel est maintenant prête à l'emploi avec :
- ✅ Authentification complète (Breeze)
- ✅ Gestion des posts, commentaires et tags
- ✅ Système d'autorisation (Policies & Gates)
- ✅ Dashboard utilisateur et admin
- ✅ Interface responsive et moderne

Bon développement ! 🚀

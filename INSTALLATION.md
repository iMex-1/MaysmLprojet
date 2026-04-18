# Instructions d'installation

## Partie 3 : Installation de Laravel Breeze

Pour installer Laravel Breeze, exécutez les commandes suivantes :

```bash
# 1. Installer Laravel Breeze
composer require laravel/breeze --dev

# 2. Installer Breeze avec Blade
php artisan breeze:install blade

# 3. Installer les dépendances NPM
npm install

# 4. Compiler les assets
npm run build

# 5. Exécuter les migrations
php artisan migrate

# 6. (Optionnel) Créer un utilisateur admin pour tester
php artisan tinker
```

Dans tinker, exécutez :
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);

\App\Models\User::create([
    'name' => 'User',
    'email' => 'user@example.com',
    'password' => bcrypt('password'),
    'role' => 'user'
]);
```

## Démarrer le serveur

```bash
php artisan serve
```

Visitez http://localhost:8000 pour voir l'application.

## Comptes de test

- Admin : admin@example.com / password
- User : user@example.com / password

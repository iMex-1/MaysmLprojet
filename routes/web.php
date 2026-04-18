<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// Route publique
Route::get('/', function () {
    return redirect()->route('posts.index');
});

// Routes publiques pour les posts (index et show)
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Groupe de routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Routes RESTful pour les posts (create, store, edit, update, destroy)
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // Dashboard utilisateur
    Route::get('/dashboard', function () {
        $posts = auth()->user()->posts()->with('comments')->latest()->get();
        return view('dashboard', compact('posts'));
    })->name('dashboard');
});

// Groupe de routes réservé aux admins
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_posts' => \App\Models\Post::count(),
            'total_comments' => \App\Models\Comment::count(),
            'recent_posts' => \App\Models\Post::with('user')->latest()->take(5)->get(),
        ];
        return view('admin.dashboard', compact('stats'));
    })->name('dashboard');
    
    // Routes admin supplémentaires
    Route::get('/users', function () {
        $users = \App\Models\User::withCount('posts')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    })->name('users');
});

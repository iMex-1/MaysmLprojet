@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="card">
    <h1>Dashboard Administrateur</h1>
    <p>Bienvenue dans l'espace d'administration</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
    <div class="card" style="text-align: center;">
        <h2 style="color: #007bff; font-size: 48px; margin: 10px 0;">{{ $stats['total_users'] }}</h2>
        <p style="color: #666;">Utilisateurs</p>
    </div>
    
    <div class="card" style="text-align: center;">
        <h2 style="color: #28a745; font-size: 48px; margin: 10px 0;">{{ $stats['total_posts'] }}</h2>
        <p style="color: #666;">Posts</p>
    </div>
    
    <div class="card" style="text-align: center;">
        <h2 style="color: #ffc107; font-size: 48px; margin: 10px 0;">{{ $stats['total_comments'] }}</h2>
        <p style="color: #666;">Commentaires</p>
    </div>
</div>

<div class="card">
    <h2>Posts récents</h2>
    
    @foreach($stats['recent_posts'] as $post)
        <div style="border-bottom: 1px solid #ddd; padding: 15px 0;">
            <h3 style="margin: 0 0 5px 0;">
                <a href="{{ route('posts.show', $post) }}" style="color: #333; text-decoration: none;">
                    {{ $post->title }}
                </a>
            </h3>
            <p style="color: #666; font-size: 14px;">
                Par {{ $post->user->name }} - {{ $post->created_at->format('d/m/Y H:i') }}
            </p>
            <form action="{{ route('posts.destroy', $post) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr ?')">
                    Supprimer
                </button>
            </form>
        </div>
    @endforeach
</div>

<div class="card">
    <a href="{{ route('admin.users') }}" class="btn">Gérer les utilisateurs</a>
    <a href="{{ route('posts.index') }}" class="btn" style="background: #6c757d;">Voir tous les posts</a>
</div>
@endsection

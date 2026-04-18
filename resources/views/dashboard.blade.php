@extends('layouts.app')

@section('title', 'Mon Dashboard')

@section('content')
<div class="card">
    <h1>Mon Dashboard</h1>
    <p>Bienvenue, {{ auth()->user()->name }} !</p>
    <p>Rôle : <strong>{{ auth()->user()->role }}</strong></p>
</div>

<div class="card">
    <h2>Mes posts ({{ $posts->count() }})</h2>
    
    @forelse($posts as $post)
        <div style="border-bottom: 1px solid #ddd; padding: 15px 0;">
            <h3 style="margin: 0 0 10px 0;">
                <a href="{{ route('posts.show', $post) }}" style="color: #333; text-decoration: none;">
                    {{ $post->title }}
                </a>
            </h3>
            <p style="color: #666; font-size: 14px; margin: 5px 0;">
                Créé le {{ $post->created_at->format('d/m/Y H:i') }} - 
                {{ $post->comments->count() }} commentaire(s)
            </p>
            <div style="margin-top: 10px;">
                <a href="{{ route('posts.edit', $post) }}" class="btn">Modifier</a>
                <form action="{{ route('posts.destroy', $post) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr ?')">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p style="color: #666;">Vous n'avez pas encore créé de post.</p>
        <a href="{{ route('posts.create') }}" class="btn">Créer mon premier post</a>
    @endforelse
</div>
@endsection

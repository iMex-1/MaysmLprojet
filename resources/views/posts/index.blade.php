@extends('layouts.app')

@section('title', 'Liste des posts')

@section('content')
<div class="card">
    <h1>Liste des posts</h1>
    
    @auth
        <a href="{{ route('posts.create') }}" class="btn">Créer un nouveau post</a>
    @endauth
</div>

@forelse($posts as $post)
    <div class="card">
        <h2>
            <a href="{{ route('posts.show', $post) }}" style="color: #333; text-decoration: none;">
                {{ $post->title }}
            </a>
        </h2>
        <p style="color: #666; font-size: 14px;">
            Par {{ $post->user->name }} - {{ $post->created_at->format('d/m/Y H:i') }}
        </p>
        <p>{{ Str::limit($post->content, 200) }}</p>
        <p style="color: #666; font-size: 14px;">
            {{ $post->comments->count() }} commentaire(s)
        </p>
        
        <div style="margin-top: 15px;">
            <a href="{{ route('posts.show', $post) }}" class="btn">Lire la suite</a>
            
            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}" class="btn">Modifier</a>
            @endcan
            
            @if(Gate::allows('delete-any-post') || Gate::allows('delete', $post))
                <form action="{{ route('posts.destroy', $post) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr ?')">
                        Supprimer
                    </button>
                </form>
            @endif
        </div>
    </div>
@empty
    <div class="card">
        <p>Aucun post disponible.</p>
    </div>
@endforelse

<div style="margin-top: 20px;">
    {{ $posts->links() }}
</div>
@endsection

@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="card">
    <h1>{{ $post->title }}</h1>
    <p style="color: #666; font-size: 14px;">
        Par {{ $post->user->name }} - {{ $post->created_at->format('d/m/Y H:i') }}
    </p>
    
    @if($post->tags->count() > 0)
        <div style="margin: 10px 0;">
            @foreach($post->tags as $tag)
                <span style="background: #007bff; color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px; margin-right: 5px;">
                    {{ $tag->name }}
                </span>
            @endforeach
        </div>
    @endif
    
    <div style="margin: 20px 0; line-height: 1.6;">
        {{ $post->content }}
    </div>
    
    <div style="margin-top: 20px;">
        <a href="{{ route('posts.index') }}" class="btn" style="background: #6c757d;">Retour</a>
        
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

<div class="card">
    <h2>Commentaires ({{ $post->comments->count() }})</h2>
    
    @forelse($post->comments as $comment)
        <div style="border-left: 3px solid #007bff; padding-left: 15px; margin: 15px 0;">
            <p style="color: #666; font-size: 14px; margin: 5px 0;">
                <strong>{{ $comment->user->name }}</strong> - {{ $comment->created_at->format('d/m/Y H:i') }}
            </p>
            <p style="margin: 10px 0;">{{ $comment->content }}</p>
        </div>
    @empty
        <p style="color: #666;">Aucun commentaire pour le moment.</p>
    @endforelse
</div>
@endsection

@extends('layouts.app')

@section('title', 'Modifier le post')

@section('content')
<div class="card">
    <h1>Modifier le post</h1>
    
    <form action="{{ route('posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required>
            @error('title')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="content">Contenu</label>
            <textarea name="content" id="content" required>{{ old('content', $post->content) }}</textarea>
            @error('content')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn">Mettre à jour</button>
        <a href="{{ route('posts.show', $post) }}" class="btn" style="background: #6c757d;">Annuler</a>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Créer un post')

@section('content')
<div class="card">
    <h1>Créer un nouveau post</h1>
    
    <form action="{{ route('posts.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required>
            @error('title')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="content">Contenu</label>
            <textarea name="content" id="content" required>{{ old('content') }}</textarea>
            @error('content')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn">Créer le post</button>
        <a href="{{ route('posts.index') }}" class="btn" style="background: #6c757d;">Annuler</a>
    </form>
</div>
@endsection

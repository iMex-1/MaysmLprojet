@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="card">
    <h1>Gestion des utilisateurs</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #6c757d;">Retour au dashboard</a>
</div>

<div class="card">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Nom</th>
                <th style="padding: 12px; text-align: left;">Email</th>
                <th style="padding: 12px; text-align: left;">Rôle</th>
                <th style="padding: 12px; text-align: left;">Posts</th>
                <th style="padding: 12px; text-align: left;">Inscription</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;">{{ $user->id }}</td>
                    <td style="padding: 12px;">{{ $user->name }}</td>
                    <td style="padding: 12px;">{{ $user->email }}</td>
                    <td style="padding: 12px;">
                        <span style="background: {{ $user->role === 'admin' ? '#dc3545' : '#007bff' }}; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px;">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td style="padding: 12px;">{{ $user->posts_count }}</td>
                    <td style="padding: 12px;">{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $users->links() }}
    </div>
</div>
@endsection

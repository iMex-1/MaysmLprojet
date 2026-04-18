<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function show($id)
    {
        // Eager loading: user -> posts -> comments -> comment user
        $user = User::with(['posts.comments.user'])
                    ->findOrFail($id);

        return view('users.show', compact('user'));
    }
}

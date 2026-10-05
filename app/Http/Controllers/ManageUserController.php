<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ManageUserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();

        return view('manage-user', [
            'user' => auth()->user(),
            'users' => $users
        ]);
    }
}
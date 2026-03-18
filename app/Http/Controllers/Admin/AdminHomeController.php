<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminHomeController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();

        return view('admin.home', compact('users'));
    }
}
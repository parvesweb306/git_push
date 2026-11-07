<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('created_by', $request->user()->id)
            ->withCount('devices')
            ->latest()
            ->get();

        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'নতুন user তৈরি করা হয়েছে।');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless(
            $user->created_by === $request->user()->id,
            403
        );

        $user->devices()->delete();
        $user->delete();

        return back()->with('success', 'User মুছে ফেলা হয়েছে।');
    }
}
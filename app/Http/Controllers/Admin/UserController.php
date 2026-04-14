<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');

        $users = User::query()
            ->when($search, function ($query, $search) {
                return $query->where('name_user', 'like', '%' . $search . '%')
                             ->orWhere('email_user', 'like', '%' . $search . '%');
            })
            ->when($role, function ($query, $role) {
                return $query->where('role', $role);
            })
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }


    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_user' => 'required|string|max:255|unique:users,name_user',
            'email_user' => 'required|string|email|max:255|unique:users,email_user',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('users.index');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cegah admin mengubah username user lain
        if (auth()->user()->id !== $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda hanya dapat mengubah data Anda sendiri.');
        }

        $request->validate([
            'name_user' => 'required|string|max:255|unique:users,name_user,' . $user->id . ',id',
            'email_user' => 'required|string|email|max:255|unique:users,email_user,' . $user->id . ',id',
            'role' => 'required|in:Admin,User',
        ]);

        $user->name_user = $request->input('name_user');
        $user->email_user = $request->input('email_user');
        $user->role = $request->input('role');
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }



    public function destroy($id)
    {
        // Cegah admin menghapus dirinya sendiri
        if (auth()->id() == $id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}

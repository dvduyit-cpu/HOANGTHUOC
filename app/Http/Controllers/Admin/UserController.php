<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', ['user' => new User()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'editor'])],
            'is_active' => ['required', 'boolean'],
        ]);

        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'Đã cấp tài khoản.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:10', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'editor'])],
            'is_active' => ['required', 'boolean'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if ($request->user()->is($user) && ! (bool) $data['is_active']) {
            return back()->withErrors(['is_active' => 'Không thể tự khóa tài khoản đang đăng nhập.']);
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'Đã cập nhật quyền tài khoản.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('roles.index', [
            'roles' => Role::query()->orderBy('name')->get(),
            'users' => User::query()->with('roles')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $name = $request->validated('name');

        Role::query()->create([
            'name' => $name,
            'slug' => Str::slug($name) ?: Str::lower($name),
        ]);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Role added.');
    }
}

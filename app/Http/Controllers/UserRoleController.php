<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserRoleController extends Controller
{
    public function store(AssignRoleRequest $request, User $user): RedirectResponse
    {
        $user->assignRole($request->validated('role'));

        return redirect()
            ->route('roles.index')
            ->with('status', "Assigned {$request->validated('role')} to {$user->name}.");
    }
}

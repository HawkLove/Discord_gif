@extends('layouts.app')

@section('title', 'Roles — Loopix')

@section('content')
    <main class="page">
        <section class="panel">
            <h2>Add a role</h2>
            <form method="POST" action="{{ route('roles.store') }}" class="inline-form">
                @csrf
                <input type="text" name="name" required maxlength="50" placeholder="moderator">
                <button class="btn btn-small" type="submit">Add role</button>
            </form>
            <p class="hint">Built-in roles: user, admin, owner. You can add more names and assign them.</p>
        </section>

        <section class="panel">
            <h2>Roles</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->slug }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h2>Assign a role</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Current</th>
                        <th>Assign</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('users.roles.store', $user) }}" class="inline-form">
                                    @csrf
                                    <select name="role" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-small" type="submit">Assign</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </main>
@endsection

@extends('layouts.app')
@section('title', 'User Accounts')
@section('content')
    <p class="eyebrow">ACCOUNT DIRECTORY</p>
    <h1>User Accounts</h1>
    <p class="lead">Sample team members and their roles in your POS workspace.</p>
    <section class="panel table-panel" aria-labelledby="directory-title">
        <div class="table-heading"><div><h2 id="directory-title">All users</h2><p>Fictional records for this learning project.</p></div><span class="count">{{ count($users) }} records</span></div>
        <div class="table-scroll" role="region" aria-label="User Accounts table" tabindex="0">
            <table>
                <caption class="sr-only">Sample users directory with id, username, name, email, role, status.</caption>
                <thead><tr><th scope="col">ID</th><th scope="col">Username</th><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Status</th></tr></thead>
                <tbody>
                @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td><span @class(['status', 'inactive' => $user->status === 'Inactive'])>{{ $user->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6">No users found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">Showing all {{ count($users) }} users <span>Database records</span></div>
    </section>
@endsection

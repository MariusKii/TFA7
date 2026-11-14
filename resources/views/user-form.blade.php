@extends('layouts.app')
@section('title', $user->exists ? 'Edit user' : 'Create user')
@section('content')
    <h1>{{ $user->exists ? 'Edit user' : 'Create user' }}</h1>
    <form class="panel account-form" method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($user->exists) @method('PUT') @endif
        <div class="form-field">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" maxlength="50" required value="{{ old('username', $user->username) }}" @error('username') aria-invalid="true" aria-describedby="username-error" @enderror>
            @error('username') <p class="field-error" id="username-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-field">
            <label for="full_name">Full name</label>
            <input id="full_name" name="full_name" type="text" maxlength="100" required value="{{ old('full_name', $user->full_name) }}" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
            @error('full_name') <p class="field-error" id="full_name-error">{{ $message }}</p> @enderror
        </div>
        @unless ($user->exists)
        <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" maxlength="255" required value="{{ old('email', $user->email) }}" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email') <p class="field-error" id="email-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-field">
            <label for="password">Password (at least 8 characters)</label>
            <input id="password" name="password" type="password" maxlength="255" required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            @error('password') <p class="field-error" id="password-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" maxlength="255" required @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror>
            @error('password_confirmation') <p class="field-error" id="password_confirmation-error">{{ $message }}</p> @enderror
        </div>
        @else
        <div class="form-field">
            @include('avatar', ['user' => $user])
            <label for="avatar">Profile picture</label>
            <input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif" aria-describedby="avatar-help @error('avatar') avatar-error @enderror" @error('avatar') aria-invalid="true" @enderror>
            <p id="avatar-help">JPG, PNG, WebP or GIF, up to 2 MB and 4096 × 4096 pixels. Leave empty to keep the current picture. Select the file again after a validation error.</p>
            @error('avatar') <p class="field-error" id="avatar-error">{{ $message }}</p> @enderror
        </div>
        @endunless
        <div class="form-actions"><button class="button" type="submit">Save user</button><a href="{{ route('users.index') }}">Cancel</a></div>
    </form>
@endsection

@extends('layouts.app')
@section('title', $customer->exists ? 'Edit customer' : 'Create customer')
@section('content')
    <h1>{{ $customer->exists ? 'Edit customer' : 'Create customer' }}</h1>
    <form class="panel account-form" method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
        @csrf
        @if ($customer->exists) @method('PUT') @endif
        <div class="form-field">
            <label for="full_name">Full name</label>
            <input id="full_name" name="full_name" type="text" maxlength="100" required value="{{ old('full_name', $customer->full_name) }}" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
            @error('full_name') <p class="field-error" id="full_name-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" maxlength="100" required value="{{ old('email', $customer->email) }}" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email') <p class="field-error" id="email-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-actions"><button class="button" type="submit">Save customer</button><a href="{{ route('customers.index') }}">Cancel</a></div>
    </form>
@endsection

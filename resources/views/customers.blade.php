@extends('layouts.app')
@section('title', 'Customer Accounts')
@section('content')
    <p class="eyebrow">ACCOUNT DIRECTORY</p>
    <h1>Customer Accounts</h1>
    <p class="lead">Sample customer contacts for your POS workspace.</p>
    <p><a class="button" href="{{ route('customers.create') }}">Add customer</a></p>
    <section class="panel table-panel" aria-labelledby="directory-title">
        <div class="table-heading"><div><h2 id="directory-title">All customers</h2><p>Fictional records for this learning project.</p></div><span class="count">{{ count($customers) }} records</span></div>
        <div class="table-scroll" role="region" aria-label="Customer Accounts table" tabindex="0">
            <table>
                <caption class="sr-only">Sample customers directory with id, name, email, phone, status.</caption>
                <thead><tr><th scope="col">ID</th><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Phone</th><th scope="col">Status</th><th scope="col">Actions</th></tr></thead>
                <tbody>
                @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->full_name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td><span @class(['status', 'inactive' => $customer->status === 'Inactive'])>{{ $customer->status }}</span></td>
                    <td><a href="{{ route('customers.edit', $customer) }}">Edit<span class="sr-only"> {{ $customer->full_name }}</span></a></td>
                </tr>
                @empty
                <tr><td colspan="6">No customers found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">Showing all {{ count($customers) }} customers <span>Database records</span></div>
    </section>
@endsection

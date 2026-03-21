@extends('layouts.admin')

@section('title', 'Tenants Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tenants Management</h1>
        <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Tenant
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tenants List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                        <tr>
                            <td>{{ $tenant->id }}</td>
                            <td><strong>{{ $tenant->name }}</strong></td>
                            <td>{{ $tenant->slug }}</td>
                            <td>{{ $tenant->email ?? 'N/A' }}</td>
                            <td>{{ $tenant->phone ?? 'N/A' }}</td>
                            <td>{{ $tenant->city ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $tenant->is_active ? 'success' : 'danger' }}">
                                    {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $tenant->users()->count() }}</td>
                            <td>
                                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.tenants.toggle-status', $tenant) }}" class="btn btn-sm btn-{{ $tenant->is_active ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $tenant->is_active ? 'ban' : 'check' }}"></i>
                                    {{ $tenant->is_active ? 'Deactivate' : 'Activate' }}
                                </a>
                                @if($tenant->users()->count() == 0)
                                    <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No tenants found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $tenants->links() }}
        </div>
    </div>
</div>
@endsection
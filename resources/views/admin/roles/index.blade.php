@extends('layouts.admin')

@section('title', 'Roles Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Roles Management</h1>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Role
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
            <h6 class="m-0 font-weight-bold text-primary">Roles List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Users Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>
                                <span class="badge badge-{{ $role->id == 1 ? 'danger' : ($role->id == 2 ? 'warning' : 'info') }}">
                                    {{ $role->name }}
                                </span>
                            </td>
                            <td>{{ $role->slug }}</td>
                            <td>{{ $role->description ?? 'N/A' }}</td>
                            <td>{{ $role->users_count ?? $role->users()->count() }}</td>
                            <td>
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @if($role->id > 3)
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
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
                            <td colspan="6" class="text-center">No roles found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection
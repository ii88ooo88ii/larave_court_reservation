@extends('layouts.admin')

@section('title', 'Courts Management')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Courts Management</h1>
        <a href="{{ route('admin.courts.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Court
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Courts List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Tenant</th>
                            <th>Type</th>
                            <th>Surface</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courts as $court)
                        <tr>
                            <td>{{ $court->id }}</td>
                            <td class="text-center">
                                @if($court->image)
                                    <img src="{{ Storage::url($court->image) }}" alt="{{ $court->name }}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                @else
                                    <div class="bg-secondary text-white rounded d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-tennis-ball"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $court->name }}</strong>
                                @if($court->is_indoor)
                                    <span class="badge badge-info">Indoor</span>
                                @endif
                                @if($court->has_floodlights)
                                    <span class="badge badge-warning">Floodlights</span>
                                @endif
                            </td>
                            <td>{{ $court->tenant->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $court->type == 'vip' ? 'danger' : ($court->type == 'premium' ? 'warning' : 'info') }}">
                                    {{ ucfirst($court->type) }}
                                </span>
                            </td>
                            <td>{{ $court->surface ? ucfirst($court->surface) : 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $court->is_active ? 'success' : 'danger' }}">
                                    {{ $court->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.courts.edit', $court) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.courts.toggle-status', $court) }}" class="btn btn-sm btn-{{ $court->is_active ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $court->is_active ? 'ban' : 'check' }}"></i>
                                    {{ $court->is_active ? 'Deactivate' : 'Activate' }}
                                </a>
                                <form action="{{ route('admin.courts.destroy', $court) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this court?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No courts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $courts->links() }}
        </div>
    </div>

@endsection
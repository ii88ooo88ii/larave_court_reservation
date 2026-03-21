@extends('layouts.admin')

@section('title', 'Pricing Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pricing Management</h1>
        <a href="{{ route('admin.pricings.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Pricing
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
            <h6 class="m-0 font-weight-bold text-primary">Pricing List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Court</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Base Price</th>
                            <th>Peak Price</th>
                            <th>Off-Peak Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </thead>
                    <tbody>
                        @forelse($pricings as $pricing)
                        <tr>
                            <td class="text-center">{{ $pricing->id }}</td>
                            <td><strong>{{ $pricing->court->name ?? 'N/A' }}</strong></td>
                            <td>{{ $pricing->name }}</td>
                            <td>
                                <span class="badge badge-{{ $pricing->type == 'peak' ? 'danger' : ($pricing->type == 'off_peak' ? 'warning' : 'info') }}">
                                    {{ ucfirst(str_replace('_', ' ', $pricing->type)) }}
                                </span>
                            </td>
                            <td>${{ number_format($pricing->base_price, 2) }}</td>
                            <td>{{ $pricing->peak_price ? '$' . number_format($pricing->peak_price, 2) : '-' }}</td>
                            <td>{{ $pricing->off_peak_price ? '$' . number_format($pricing->off_peak_price, 2) : '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $pricing->is_active ? 'success' : 'danger' }}">
                                    {{ $pricing->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.pricings.edit', $pricing) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.pricings.toggle-status', $pricing) }}" class="btn btn-sm btn-{{ $pricing->is_active ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $pricing->is_active ? 'ban' : 'check' }}"></i>
                                    {{ $pricing->is_active ? 'Deactivate' : 'Activate' }}
                                </a>
                                <form action="{{ route('admin.pricings.destroy', $pricing) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No pricing found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $pricings->links() }}
        </div>
    </div>
</div>
@endsection
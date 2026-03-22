@extends('layouts.admin')

@section('title', 'Additional Fees Management')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Additional Fees Management</h1>
        <a href="{{ route('admin.additional-fees.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Fee
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
            <h6 class="m-0 font-weight-bold text-primary">Additional Fees List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pricing</th>
                            <th>Fee Name</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Mandatory</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </thead>
                    <tbody>
                        @forelse($fees as $fee)
                        <tr>
                            <td class="text-center">{{ $fee->id }}</td>
                            <td><strong>{{ $fee->pricing->name ?? 'N/A' }}</strong><br>
                                <small>{{ $fee->pricing->court->name ?? '' }}</small>
                            </td>
                            <td>{{ $fee->name }}</td>
                            <td>
                                <span class="badge badge-{{ $fee->type == 'percentage' ? 'info' : 'primary' }}">
                                    {{ ucfirst($fee->type) }}
                                </span>
                            </td>
                            <td>{{ $fee->type == 'percentage' ? $fee->amount . '%' : '$' . number_format($fee->amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $fee->is_mandatory ? 'warning' : 'secondary' }}">
                                    {{ $fee->is_mandatory ? 'Mandatory' : 'Optional' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $fee->is_active ? 'success' : 'danger' }}">
                                    {{ $fee->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.additional-fees.edit', $fee) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.additional-fees.toggle-status', $fee) }}" class="btn btn-sm btn-{{ $fee->is_active ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $fee->is_active ? 'ban' : 'check' }}"></i>
                                    {{ $fee->is_active ? 'Deactivate' : 'Activate' }}
                                </a>
                                <form action="{{ route('admin.additional-fees.destroy', $fee) }}" method="POST" class="d-inline">
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
                            <td colspan="8" class="text-center">No additional fees found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $fees->links() }}
        </div>
    </div>

@endsection
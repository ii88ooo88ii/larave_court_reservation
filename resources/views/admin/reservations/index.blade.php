@extends('layouts.admin')

@section('title', 'Reservations')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reservations</h1>
        <div>
            <a href="{{ route('admin.reservations.calendar') }}" class="btn btn-info btn-sm mr-2">
                <i class="fas fa-calendar-alt"></i> Calendar View
            </a>
            <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Reservation
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Reservation List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Court</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </thead>
                    <tbody>
                        @forelse($reservations as $reservation)
                        <tr>
                            <td>
                                <strong>{{ $reservation->reservation_code }}</strong>
                                <br>
                                <small class="text-muted">ID: #{{ $reservation->id }}</small>
                             </td>
                            <td>{{ $reservation->court->name ?? 'N/A' }}</td>
                            <td>{{ $reservation->user->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('M d, Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} - 
                                {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}
                                <br>
                                <small class="text-muted">{{ $reservation->duration_hours }} hour(s)</small>
                            </td>
                            <td>${{ number_format($reservation->total_amount, 2) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'ongoing' => 'info',
                                        'completed' => 'secondary',
                                        'cancelled' => 'danger',
                                        'extended' => 'primary'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $statusColors[$reservation->status] ?? 'secondary' }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                                @if($reservation->is_extended)
                                    <span class="badge badge-info">Extended</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($reservation->status != 'cancelled' && $reservation->status != 'completed')
                                <a href="{{ route('admin.reservations.edit', $reservation) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No reservations found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $reservations->links() }}
        </div>
    </div>

@endsection
@extends('layouts.admin')

@section('title', 'Reservation Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reservation Details</h1>
        <div>
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Reservation Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Reservation Code:</strong> {{ $reservation->reservation_code }}</p>
                            <p><strong>Court:</strong> {{ $reservation->court->name ?? 'N/A' }}</p>
                            <p><strong>Court Type:</strong> {{ $reservation->court->courtType->name ?? 'N/A' }}</p>
                            <p><strong>User:</strong> {{ $reservation->user->name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $reservation->user->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('l, F d, Y') }}</p>
                            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} - 
                               {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}</p>
                            <p><strong>Duration:</strong> {{ $reservation->duration_hours }} hour(s)</p>
                            <p><strong>Status:</strong> 
                                <span class="badge badge-{{ $reservation->status == 'confirmed' ? 'success' : ($reservation->status == 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($reservation->notes)
                    <hr>
                    <p><strong>Notes:</strong> {{ $reservation->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Details</h6>
                </div>
                <div class="card-body">
                    <p><strong>Duration:</strong> {{ $reservation->duration_hours }} hour(s)</p>
                    <p><strong>Base Price:</strong> ${{ number_format($reservation->base_price, 2) }}</p>
                    <p><strong>Additional Fees:</strong> ${{ number_format($reservation->additional_fees_total, 2) }}</p>
                    <hr>
                    <h5><strong>Total Amount:</strong> ${{ number_format($reservation->total_amount, 2) }}</h5>
                    
                    @php
                        $feesBreakdown = $reservation->additional_fees_breakdown;
                        // Decode if it's a JSON string
                        if (is_string($feesBreakdown)) {
                            $feesBreakdown = json_decode($feesBreakdown, true);
                        }
                    @endphp
                    
                    @if($feesBreakdown && count($feesBreakdown) > 0)
                        <hr>
                        <p><strong>Fee Breakdown:</strong></p>
                        @foreach($feesBreakdown as $fee)
                            <small class="d-block">• {{ $fee['name'] ?? 'Unknown' }}: ${{ number_format($fee['calculated_amount'] ?? 0, 2) }}
                                @if(isset($fee['type']) && $fee['type'] === 'percentage')
                                    ({{ $fee['amount'] ?? 0 }}%)
                                @endif
                            </small>
                        @endforeach
                    @endif
                </div>
            </div>

            @if($reservation->status == 'confirmed')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#cancelModal">
                        <i class="fas fa-times"></i> Cancel Reservation
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Reservation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.reservations.update', $reservation) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cancellation Reason</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <input type="hidden" name="status" value="cancelled">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Reservation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
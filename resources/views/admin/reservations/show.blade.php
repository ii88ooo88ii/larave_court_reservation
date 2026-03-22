@extends('layouts.admin')

@section('title', 'Reservation Details')

@section('content')
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
                            @if($reservation->is_extended)
                                <p><strong>Extended:</strong> Yes ({{ $reservation->extension_count }} time(s))</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Details</h6>
                </div>
                <div class="card-body">
                    <p><strong>Base Price:</strong> ${{ number_format($reservation->base_price, 2) }}</p>
                    <p><strong>Additional Fees:</strong> ${{ number_format($reservation->additional_fees_total, 2) }}</p>
                    <hr>
                    <h5><strong>Total Amount:</strong> ${{ number_format($reservation->total_amount, 2) }}</h5>
                    
                    @if($reservation->additional_fees_breakdown)
                        <hr>
                        <p><strong>Fee Breakdown:</strong></p>
                        @foreach($reservation->additional_fees_breakdown as $fee)
                            <small class="d-block">• {{ $fee['name'] }}: ${{ number_format($fee['calculated_amount'], 2) }}</small>
                        @endforeach
                    @endif
                </div>
            </div>

            @if($reservation->status == 'confirmed' || $reservation->status == 'ongoing')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-warning btn-block mb-2" data-toggle="modal" data-target="#extendModal">
                        <i class="fas fa-clock"></i> Extend Reservation
                    </button>
                    <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#cancelModal">
                        <i class="fas fa-times"></i> Cancel Reservation
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>


<!-- Extend Modal -->
<div class="modal fade" id="extendModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extend Reservation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="extendForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Additional Hours</label>
                        <select name="additional_hours" class="form-control" required>
                            <option value="1">1 Hour</option>
                            <option value="2">2 Hours</option>
                            <option value="3">3 Hours</option>
                            <option value="4">4 Hours</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Additional Cost ($)</label>
                        <input type="number" step="0.01" name="additional_cost" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Extend</button>
                </div>
            </form>
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
            <form action="{{ route('admin.reservations.cancel', $reservation) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cancellation Reason</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Reservation</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#extendForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("admin.reservations.extend", $reservation) }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message || 'Error extending reservation');
            }
        });
    });
</script>
@endpush
@endsection
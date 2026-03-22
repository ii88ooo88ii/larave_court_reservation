@extends('layouts.admin')

@section('title', 'Create Reservation')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Reservation</h1>
        <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Reservation Information</h6>
        </div>
        <div class="card-body">
            <form id="reservationForm" action="{{ route('admin.reservations.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="court_id">Court <span class="text-danger">*</span></label>
                            <select name="court_id" id="court_id" class="form-control" required>
                                <option value="">Select Court</option>
                                @foreach($courts as $court)
                                    <option value="{{ $court->id }}">
                                        {{ $court->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id">User <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="reservation_date">Reservation Date <span class="text-danger">*</span></label>
                            <input type="date" name="reservation_date" id="reservation_date" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_time">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="start_time" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_time">End Time <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" id="end_time" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <!-- Price Summary Section -->
                <div class="card mt-3 mb-3 bg-light" id="priceSummary" style="display: none;">
                    <div class="card-body" id="priceDetails">
                        <!-- Price summary will appear here -->
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes (Optional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                </div>
                
                <!-- Hidden inputs for price data -->
                <input type="hidden" name="base_price" id="base_price_input">
                <input type="hidden" name="additional_fees_total" id="additional_fees_total_input">
                <input type="hidden" name="total_amount" id="total_amount_input">
                <input type="hidden" name="additional_fees_breakdown" id="additional_fees_breakdown_input">
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-save"></i> Create Reservation
                    </button>
                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let calculationTimeout;
        
        // Helper function to format currency
        function formatCurrency(amount, currency = 'USD') {
            if (typeof amount === 'string') {
                amount = parseFloat(amount);
            }
            if (isNaN(amount)) amount = 0;
            return currency + ' ' + amount.toFixed(2);
        }
        
        // Helper function to parse number safely
        function parseNumber(value) {
            if (typeof value === 'string') {
                return parseFloat(value) || 0;
            }
            return value || 0;
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Function to calculate price
        function calculatePrice() {
            const courtId = $('#court_id').val();
            const date = $('#reservation_date').val();
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();
            
            // Validate all fields are filled
            if (!courtId || !date || !startTime || !endTime) {
                $('#priceSummary').hide();
                $('#submitBtn').prop('disabled', true);
                return;
            }
            
            // Validate end time is after start time
            if (startTime >= endTime) {
                $('#priceSummary').hide();
                $('#submitBtn').prop('disabled', true);
                alert('End time must be after start time');
                return;
            }
            
            // Show loading
            $('#priceSummary').show();
            $('#priceDetails').html('<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Calculating price...</div>');
            
            $.ajax({
                url: '{{ route("admin.reservations.calculate-price") }}',
                method: 'POST',
                data: {
                    court_id: courtId,
                    reservation_date: date,
                    start_time: startTime,
                    end_time: endTime,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Response:', response);
                    
                    if (response.success) {
                        // Parse all numeric values safely
                        const duration = parseInt(response.duration) || 0;
                        const pricePerHour = parseNumber(response.price_per_hour);
                        const basePrice = parseNumber(response.base_price);
                        const totalAmount = parseNumber(response.total_amount);
                        const additionalFeesTotal = parseNumber(response.additional_fees_total);
                        const currency = response.currency || 'USD';
                        const priceType = response.price_type || 'Standard Rate';
                        
                        // Build additional fees HTML
                        let feesHtml = '';
                        if (response.additional_fees_breakdown && response.additional_fees_breakdown.length > 0) {
                            feesHtml = '<ul class="list-unstyled small mb-0">';
                            response.additional_fees_breakdown.forEach(function(fee) {
                                const feeAmount = parseNumber(fee.calculated_amount);
                                feesHtml += '<li>• ' + escapeHtml(fee.name) + ': ' + formatCurrency(feeAmount, currency);
                                if (fee.type === 'percentage') {
                                    const feePercent = parseNumber(fee.amount);
                                    feesHtml += ' (' + feePercent + '%)';
                                }
                                feesHtml += '</li>';
                            });
                            feesHtml += '</ul>';
                        } else {
                            feesHtml = '<p class="text-muted small mb-0">No additional fees</p>';
                        }
                        
                        // Update the price summary
                        const priceHtml = `
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Duration:</strong> ${duration} hour(s)</p>
                                    <p class="mb-2"><strong>Rate Type:</strong> <span class="badge badge-info">${escapeHtml(priceType)}</span></p>
                                    <p class="mb-2"><strong>Price per Hour:</strong> ${formatCurrency(pricePerHour, currency)}</p>
                                    <p class="mb-2"><strong>Base Price:</strong> ${formatCurrency(basePrice, currency)}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Additional Fees:</strong></p>
                                    <div id="additionalFeesList">${feesHtml}</div>
                                    <hr class="my-2">
                                    <h5 class="text-primary mb-0"><strong>Total Amount:</strong> ${formatCurrency(totalAmount, currency)}</h5>
                                </div>
                            </div>
                        `;
                        
                        $('#priceDetails').html(priceHtml);
                        
                        // Set hidden inputs - ensure additional_fees_breakdown is JSON string
                        $('#base_price_input').val(basePrice);
                        $('#additional_fees_total_input').val(additionalFeesTotal);
                        $('#total_amount_input').val(totalAmount);
                        $('#additional_fees_breakdown_input').val(JSON.stringify(response.additional_fees_breakdown || []));
                        
                        $('#submitBtn').prop('disabled', false);
                        $('#priceSummary').show();
                    } else {
                        $('#priceDetails').html('<div class="alert alert-warning mb-0">' + escapeHtml(response.message || 'Unable to calculate price') + '</div>');
                        $('#submitBtn').prop('disabled', true);
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    let errorMsg = 'Error calculating price. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#priceDetails').html('<div class="alert alert-danger mb-0">' + escapeHtml(errorMsg) + '</div>');
                    $('#submitBtn').prop('disabled', true);
                }
            });
        }
        
        // Trigger calculation when any field changes
        $('#court_id, #reservation_date, #start_time, #end_time').on('change', function() {
            clearTimeout(calculationTimeout);
            calculationTimeout = setTimeout(calculatePrice, 500);
        });
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        $('#reservation_date').attr('min', today);
        
        // Initial trigger if fields have values
        if ($('#court_id').val() && $('#reservation_date').val() && $('#start_time').val() && $('#end_time').val()) {
            calculatePrice();
        }
    });
</script>
@endpush
@endsection
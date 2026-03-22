@extends('layouts.admin')

@section('title', 'Create Additional Fee')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Additional Fee</h1>
        <a href="{{ route('admin.additional-fees.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please check the form for errors.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Fee Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.additional-fees.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pricing_id">Pricing <span class="text-danger">*</span></label>
                            <select name="pricing_id" id="pricing_id" class="form-control @error('pricing_id') is-invalid @enderror" required>
                                <option value="">Select Pricing</option>
                                @foreach($pricings as $pricing)
                                    <option value="{{ $pricing->id }}" {{ old('pricing_id', $selectedPricing) == $pricing->id ? 'selected' : '' }}>
                                        {{ $pricing->name }} - {{ $pricing->court->name ?? '' }} ({{ ucfirst($pricing->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pricing_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Fee Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="type">Fee Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($feeTypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                            <small class="form-text text-muted">For percentage, enter number (e.g., 10 for 10%)</small>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_mandatory" value="1" class="form-check-input" id="is_mandatory" {{ old('is_mandatory', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_mandatory">Mandatory Fee</label>
                                <small class="form-text text-muted d-block">If unchecked, user can choose to add this fee</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    <small class="form-text text-muted">Example: Court maintenance fee, guest fee, equipment rental, etc.</small>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Fee
                    </button>
                    <a href="{{ route('admin.additional-fees.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection
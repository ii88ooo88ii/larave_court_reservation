@extends('layouts.admin')

@section('title', 'Edit Pricing')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pricing: {{ $pricing->name }}</h1>
        <a href="{{ route('admin.pricings.index') }}" class="btn btn-secondary btn-sm">
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
            <h6 class="m-0 font-weight-bold text-primary">Pricing Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pricings.update', $pricing) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="court_id">Court <span class="text-danger">*</span></label>
                            <select name="court_id" id="court_id" class="form-control @error('court_id') is-invalid @enderror" required>
                                <option value="">Select Court</option>
                                @foreach($courts as $court)
                                    <option value="{{ $court->id }}" {{ old('court_id', $pricing->court_id) == $court->id ? 'selected' : '' }}>
                                        {{ $court->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('court_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Pricing Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $pricing->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="type">Pricing Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($pricingTypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('type', $pricing->type) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="base_price">Base Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="base_price" id="base_price" class="form-control @error('base_price') is-invalid @enderror" value="{{ old('base_price', $pricing->base_price) }}" required>
                            @error('base_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="currency">Currency <span class="text-danger">*</span></label>
                            <input type="text" name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $pricing->currency) }}" required>
                            @error('currency')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="peak_price">Peak Price ($)</label>
                            <input type="number" step="0.01" name="peak_price" id="peak_price" class="form-control" value="{{ old('peak_price', $pricing->peak_price) }}">
                            <small class="form-text text-muted">For peak hours (if applicable)</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="off_peak_price">Off-Peak Price ($)</label>
                            <input type="number" step="0.01" name="off_peak_price" id="off_peak_price" class="form-control" value="{{ old('off_peak_price', $pricing->off_peak_price) }}">
                            <small class="form-text text-muted">For off-peak hours (if applicable)</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="minimum_hours">Minimum Hours <span class="text-danger">*</span></label>
                            <input type="number" name="minimum_hours" id="minimum_hours" class="form-control @error('minimum_hours') is-invalid @enderror" value="{{ old('minimum_hours', $pricing->minimum_hours) }}" required>
                            @error('minimum_hours')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_time">Start Time</label>
                            <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', $pricing->start_time ? date('H:i', strtotime($pricing->start_time)) : '') }}">
                            <small class="form-text text-muted">For time-based pricing</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_time">End Time</label>
                            <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', $pricing->end_time ? date('H:i', strtotime($pricing->end_time)) : '') }}">
                            <small class="form-text text-muted">For time-based pricing</small>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="days_of_week">Days of Week</label>
                    <div class="row">
                        @foreach($daysOfWeek as $key => $day)
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="days_of_week[]" value="{{ $key }}" id="day_{{ $key }}"
                                    {{ in_array($key, old('days_of_week', json_decode($pricing->days_of_week ?? '[]', true))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="day_{{ $key }}">{{ $day }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <small class="form-text text-muted">Select days this pricing applies to (leave blank for all days)</small>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $pricing->description) }}</textarea>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $pricing->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Pricing
                    </button>
                    <a href="{{ route('admin.pricings.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
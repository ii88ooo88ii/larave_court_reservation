@extends('layouts.admin')

@section('title', 'Edit Court')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Court: {{ $court->name }}</h1>
        <a href="{{ route('admin.courts.index') }}" class="btn btn-secondary btn-sm">
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
            <h6 class="m-0 font-weight-bold text-primary">Court Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.courts.update', $court) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tenant_id">Tenant <span class="text-danger">*</span></label>
                            <select class="form-control @error('tenant_id') is-invalid @enderror" name="tenant_id" required>
                                <option value="">Select Tenant</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id', $court->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="court_type_id">Court Type <span class="text-danger">*</span></label>
                            <select class="form-control @error('court_type_id') is-invalid @enderror" name="court_type_id" required>
                                <option value="">Select Court Type</option>
                                @foreach($courtTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('court_type_id', $court->court_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('court_type_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Court Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $court->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">Court Classification <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" name="type" required>
                                <option value="">Select Classification</option>
                                @foreach($courtClassification as $key => $value)
                                    <option value="{{ $key }}" {{ old('type', $court->type) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hourly_rate">Hourly Rate ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('hourly_rate') is-invalid @enderror" name="hourly_rate" value="{{ old('hourly_rate', $court->hourly_rate) }}" required>
                            @error('hourly_rate')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="capacity">Capacity (Players) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" name="capacity" value="{{ old('capacity', $court->capacity) }}" required min="1" max="10">
                            @error('capacity')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="surface">Surface Type</label>
                            <select class="form-control @error('surface') is-invalid @enderror" name="surface">
                                <option value="">Select Surface</option>
                                @foreach($surfaces as $key => $value)
                                    <option value="{{ $key }}" {{ old('surface', $court->surface) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('surface')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Features</label>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input" name="is_indoor" value="1" id="is_indoor" {{ old('is_indoor', $court->is_indoor) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_indoor">Indoor Court</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input" name="has_floodlights" value="1" id="has_floodlights" {{ old('has_floodlights', $court->has_floodlights) ? 'checked' : '' }}>
                        <label class="form-check-label" for="has_floodlights">Has Floodlights</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $court->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="image">Court Image</label>
                    @if($court->image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($court->image) }}" alt="{{ $court->name }}" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
                        </div>
                    @endif
                    <input type="file" class="form-control-file @error('image') is-invalid @enderror" name="image" accept="image/*">
                    <small class="form-text text-muted">Allowed formats: JPEG, PNG, JPG. Max size: 2MB. Leave blank to keep current image.</small>
                    @error('image')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active" {{ old('is_active', $court->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Court
                    </button>
                    <a href="{{ route('admin.courts.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
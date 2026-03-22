@extends('layouts.admin')

@section('title', 'Create Court Type')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Court Type</h1>
        <a href="{{ route('admin.court-types.index') }}" class="btn btn-secondary btn-sm">
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
            <h6 class="m-0 font-weight-bold text-primary">Court Type Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.court-types.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tenant_id">Tenant <span class="text-danger">*</span></label>
                            <select name="tenant_id" id="tenant_id" class="form-control @error('tenant_id') is-invalid @enderror" required>
                                <option value="">Select Tenant</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
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
                            <label for="name">Court Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="icon">Icon <span class="text-danger">*</span></label>
                            <select name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror" required>
                                <option value="">Select Icon</option>
                                @foreach($icons as $key => $value)
                                    <option value="{{ $key }}" {{ old('icon') == $key ? 'selected' : '' }}>
                                        <i class="fas {{ $key }}"></i> {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Select an icon that represents this court type</small>
                            @error('icon')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="color" name="color" id="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', '#4e73df') }}">
                            <small class="form-text text-muted">Choose a color for the icon</small>
                            @error('color')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Court Type
                    </button>
                    <a href="{{ route('admin.court-types.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>


@push('scripts')
<script>
    // Preview icon selection
    document.getElementById('icon').addEventListener('change', function() {
        const selectedIcon = this.value;
        if (selectedIcon) {
            const preview = document.createElement('div');
            preview.className = 'mt-2 p-2 bg-light rounded text-center';
            preview.innerHTML = `<i class="fas ${selectedIcon} fa-3x" style="color: ${document.getElementById('color').value}"></i>`;
            
            const oldPreview = document.querySelector('.icon-preview');
            if (oldPreview) oldPreview.remove();
            
            preview.classList.add('icon-preview');
            this.parentNode.appendChild(preview);
        }
    });
    
    // Update icon color preview
    document.getElementById('color').addEventListener('input', function() {
        const preview = document.querySelector('.icon-preview i');
        if (preview) {
            preview.style.color = this.value;
        }
    });
</script>
@endpush
@endsection
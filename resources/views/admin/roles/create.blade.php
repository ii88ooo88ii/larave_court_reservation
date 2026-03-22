@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Role</h1>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Role Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug') }}" required>
                            <small class="form-text text-muted">Example: admin, manager, user (lowercase, no spaces)</small>
                            @error('slug')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Permissions Section -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Role Permissions</h6>
                        <small class="text-muted">Select the permissions this role will have</small>
                    </div>
                    <div class="card-body">
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input select-all" 
                                           id="select_all_{{ $module }}" data-module="{{ $module }}">
                                    <label class="custom-control-label font-weight-bold text-capitalize" 
                                           for="select_all_{{ $module }}">
                                        {{ ucfirst($module) }} Module
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($modulePermissions as $permission)
                                    <div class="col-md-3 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                   class="custom-control-input permission-checkbox permission-{{ $module }}" 
                                                   id="permission_{{ $permission->id }}"
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   {{ old('permissions') && in_array($permission->id, old('permissions')) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                                <br>
                                                <small class="text-muted">{{ $permission->description }}</small>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Role
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>


@push('scripts')
<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        let slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    });
    
    // Select all functionality for each module
    document.querySelectorAll('.select-all').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const module = this.dataset.module;
            const checkboxes = document.querySelectorAll(`.permission-${module}`);
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });
</script>
@endpush
@endsection
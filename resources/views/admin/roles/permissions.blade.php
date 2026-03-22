@extends('layouts.admin')

@section('title', 'Manage Role Permissions')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Permissions: {{ $role->name }}</h1>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Role Permissions</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.update-permissions', $role) }}" method="POST">
                @csrf
                
                @foreach($permissions as $module => $modulePermissions)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-capitalize">{{ $module }} Module</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($modulePermissions as $permission)
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="permission_{{ $permission->id }}"
                                           name="permissions[]" 
                                           value="{{ $permission->id }}"
                                           {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->name }}
                                        <small class="text-muted d-block">{{ $permission->description }}</small>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Permissions
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection
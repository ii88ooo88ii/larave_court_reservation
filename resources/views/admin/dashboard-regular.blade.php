@extends('layouts.admin')

@section('title', 'My Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Dashboard</h1>
    </div>

    <!-- Welcome Message -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Welcome, {{ Auth::user()->name }}!</strong> 
        You are logged in as a Regular User.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <!-- User Profile Card -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                My Name
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                My Username
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Auth::user()->username }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-at fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                My Email
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role and Tenant Information -->
    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Role</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-tag fa-3x text-primary mb-3"></i>
                        <h4>{{ Auth::user()->role ? Auth::user()->role->name : 'No Role Assigned' }}</h4>
                        <p class="text-muted">{{ Auth::user()->role ? Auth::user()->role->description : 'No description available' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Tenant/Organization</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-building fa-3x text-success mb-3"></i>
                        <h4>{{ Auth::user()->tenant ? Auth::user()->tenant->name : 'No Tenant Assigned' }}</h4>
                        @if(Auth::user()->tenant)
                            <p class="text-muted">
                                {{ Auth::user()->tenant->address ? Auth::user()->tenant->address . ', ' : '' }}
                                {{ Auth::user()->tenant->city ? Auth::user()->tenant->city . ', ' : '' }}
                                {{ Auth::user()->tenant->country ? Auth::user()->tenant->country : '' }}
                            </p>
                            <p class="small text-muted">
                                <i class="fas fa-phone"></i> {{ Auth::user()->tenant->phone ?? 'No phone' }}<br>
                                <i class="fas fa-envelope"></i> {{ Auth::user()->tenant->email ?? 'No email' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information (Read-only) -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Users
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\User::count() }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Active Tenants
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Tenant::where('is_active', true)->count() }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    System Roles
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Role::count() }}</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center text-muted">
                        <small>You have read-only access to system statistics. Contact your administrator for more information.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Settings -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#changePasswordModal">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-info btn-block" data-toggle="modal" data-target="#updateProfileModal">
                                <i class="fas fa-user-edit"></i> Update Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('change-password') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Profile Modal -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('update-profile') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="{{ Auth::user()->username }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
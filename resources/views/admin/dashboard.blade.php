@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i>
                    PDF Report
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-file-excel fa-sm fa-fw mr-2 text-gray-400"></i>
                    Excel Report
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-print fa-sm fa-fw mr-2 text-gray-400"></i>
                    Print
                </a>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Welcome back, {{ Auth::user()->name }}!</strong> 
        @if(Auth::user()->isAdmin())
            You have full access to manage the system.
        @elseif(Auth::user()->isManager())
            You have limited access to manage users and tenants.
        @else
            You have view-only access.
        @endif
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <!-- Content Row - Statistics Cards -->
    <div class="row">
        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 
                {{ $canViewUsers ? 'clickable-card' : '' }}" 
                onclick="{{ $canViewUsers ? "window.location.href='" . route('admin.users.index') . "'" : '' }}"
                style="{{ $canViewUsers ? 'cursor: pointer;' : '' }}">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUsers) }}</div>
                            @if($totalUsers > 0)
                                <div class="small text-success mt-2">
                                    <i class="fas fa-arrow-up"></i> Active users
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 
                {{ $canViewRoles ? 'clickable-card' : '' }}" 
                onclick="{{ $canViewRoles ? "window.location.href='" . route('admin.roles.index') . "'" : '' }}"
                style="{{ $canViewRoles ? 'cursor: pointer;' : '' }}">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Roles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRoles) }}</div>
                            <div class="small text-muted mt-2">
                                System roles configured
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenants Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 
                {{ $canViewTenants ? 'clickable-card' : '' }}" 
                onclick="{{ $canViewTenants ? "window.location.href='" . route('admin.tenants.index') . "'" : '' }}"
                style="{{ $canViewTenants ? 'cursor: pointer;' : '' }}">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Tenants
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalTenants) }}</div>
                            <div class="small text-success mt-2">
                                <i class="fas fa-check-circle"></i> {{ $activeTenants }} active
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                System Health
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">100%</div>
                            <div class="small text-success mt-2">
                                <i class="fas fa-check-circle"></i> All systems operational
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Charts and Tables -->
    <div class="row">
        <!-- Users by Role Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Users by Role</h6>
                    @if($canViewUsers)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="usersByRoleChart"></canvas>
                    </div>
                    <hr>
                    <div class="mt-3">
                        @foreach($usersByRole as $role)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>{{ $role['name'] }}</span>
                                <span>{{ $role['count'] }} users</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                     style="width: {{ $totalUsers > 0 ? ($role['count'] / $totalUsers * 100) : 0 }}%"
                                     aria-valuenow="{{ $role['count'] }}" aria-valuemin="0" aria-valuemax="{{ $totalUsers }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Users by Tenant Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Users by Tenant</h6>
                    @if($canViewTenants)
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="usersByTenantChart"></canvas>
                    </div>
                    <hr>
                    <div class="mt-3">
                        @foreach($usersByTenant as $tenant)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>{{ $tenant['name'] }}</span>
                                <span>{{ $tenant['count'] }} users</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $totalUsers > 0 ? ($tenant['count'] / $totalUsers * 100) : 0 }}%"
                                     aria-valuenow="{{ $tenant['count'] }}" aria-valuemin="0" aria-valuemax="{{ $totalUsers }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users Table -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                    @if($canViewUsers)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Tenant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->role_id == 1 ? 'danger' : ($user->role_id == 2 ? 'warning' : 'info') }}">
                                            {{ $user->role->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->tenant->name ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No users found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    @if(Auth::user()->isAdmin())
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus"></i> Add New User
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-tag"></i> Create New Role
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.tenants.create') }}" class="btn btn-info btn-block">
                                <i class="fas fa-building"></i> Add New Tenant
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-warning btn-block" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt"></i> Refresh Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .clickable-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .clickable-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .card {
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Users by Role Chart
    const ctx1 = document.getElementById('usersByRoleChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($usersByRole->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($usersByRole->pluck('count')) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Users by Tenant Chart
    const ctx2 = document.getElementById('usersByTenantChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: {!! json_encode($usersByTenant->pluck('name')) !!},
            datasets: [{
                label: 'Users',
                data: {!! json_encode($usersByTenant->pluck('count')) !!},
                backgroundColor: '#4e73df',
                borderColor: '#2e59d9',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush
@endsection
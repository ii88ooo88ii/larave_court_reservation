@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reports</h1>
        <a href="{{ route('admin.reports.export', request()->all()) }}" class="btn btn-success btn-sm">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Report Type</label>
                        <select name="type" class="form-control">
                            <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ request('type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date/Period</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tenant (Optional)</label>
                        <select name="tenant_id" class="form-control">
                            <option value="">All Tenants</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-chart-line"></i> Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Reservations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($reportData['summary']['total_reservations']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Hours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($reportData['summary']['total_hours']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($reportData['summary']['total_revenue'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Amount
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($reportData['summary']['average_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Info -->
    <div class="alert alert-info">
        <strong>Period:</strong> {{ $reportData['period_info']['label'] }}<br>
        <strong>Date Range:</strong> {{ $reportData['period_info']['start'] }} to {{ $reportData['period_info']['end'] }}
    </div>

    <!-- Detailed Breakdown -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Reservations</th>
                                    <th>Hours</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData['grouped_data'] as $data)
                                <tr>
                                    <td class="text-center">{{ $data['period'] }}</td>
                                    <td class="text-center">{{ number_format($data['count']) }}</td>
                                    <td class="text-center">{{ number_format($data['hours']) }}</td>
                                    <td class="text-center">${{ number_format($data['revenue'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Performing Courts</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Court</th>
                                    <th>Reservations</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData['top_courts'] as $court)
                                <tr>
                                    <td>{{ $court->court->name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $court->total_reservations }}</td>
                                    <td class="text-center">${{ number_format($court->total_revenue, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
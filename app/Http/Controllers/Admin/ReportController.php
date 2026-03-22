<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Only administrators can access reports.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $tenants = Tenant::where('is_active', true)->get();
        $selectedTenant = $request->get('tenant_id');
        $reportType = $request->get('type', 'daily');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $reportData = $this->generateReport($reportType, $date, $selectedTenant);
        
        return view('admin.reports.index', compact('tenants', 'selectedTenant', 'reportType', 'date', 'reportData'));
    }

    private function generateReport($type, $date, $tenantId = null)
    {
        $query = Reservation::with('court')
            ->where('status', '!=', 'cancelled');
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        // Apply date filters
        switch ($type) {
            case 'daily':
                $query->whereDate('reservation_date', $date);
                break;
            case 'weekly':
                $startOfWeek = Carbon::parse($date)->startOfWeek();
                $endOfWeek = Carbon::parse($date)->endOfWeek();
                $query->whereBetween('reservation_date', [$startOfWeek, $endOfWeek]);
                break;
            case 'monthly':
                $query->whereYear('reservation_date', Carbon::parse($date)->year)
                      ->whereMonth('reservation_date', Carbon::parse($date)->month);
                break;
            case 'yearly':
                $query->whereYear('reservation_date', Carbon::parse($date)->year);
                break;
        }
        
        $reservations = $query->get();
        
        // Summary
        $summary = [
            'total_reservations' => $reservations->count(),
            'total_hours' => $reservations->sum('duration_hours'),
            'total_revenue' => $reservations->sum('total_amount'),
            'average_duration' => $reservations->count() > 0 ? round($reservations->avg('duration_hours'), 2) : 0,
            'average_amount' => $reservations->count() > 0 ? round($reservations->avg('total_amount'), 2) : 0,
        ];
        
        // Generate periods
        $groupedData = $this->generatePeriods($type, $date);
        
        // Fill data
        foreach ($reservations as $reservation) {
            $dateObj = Carbon::parse($reservation->reservation_date);
            
            switch ($type) {
                case 'daily':
                    $key = Carbon::parse($reservation->start_time)->format('H:00');
                    break;
                case 'weekly':
                    $key = $dateObj->format('l');
                    break;
                case 'monthly':
                    $key = (string)$dateObj->day;
                    break;
                case 'yearly':
                    $key = $dateObj->format('F');
                    break;
                default:
                    $key = Carbon::parse($reservation->start_time)->format('H:00');
            }
            
            if (isset($groupedData[$key])) {
                $groupedData[$key]['count']++;
                $groupedData[$key]['hours'] += $reservation->duration_hours;
                $groupedData[$key]['revenue'] += $reservation->total_amount;
            }
        }
        
        // Top courts
        $topCourts = $reservations->groupBy('court_id')
            ->map(function($items) {
                $court = $items->first()->court;
                return (object)[
                    'court' => $court,
                    'total_reservations' => $items->count(),
                    'total_revenue' => $items->sum('total_amount'),
                ];
            })
            ->sortByDesc('total_reservations')
            ->take(5);
        
        return [
            'summary' => $summary,
            'grouped_data' => array_values($groupedData),
            'top_courts' => $topCourts,
            'period_info' => $this->getPeriodInfo($type, $date)
        ];
    }
    
    private function generatePeriods($type, $date)
    {
        $periods = [];
        
        switch ($type) {
            case 'daily':
                for ($hour = 0; $hour < 24; $hour++) {
                    $periods[sprintf('%02d:00', $hour)] = [
                        'period' => sprintf('%02d:00', $hour),
                        'count' => 0,
                        'hours' => 0,
                        'revenue' => 0,
                    ];
                }
                break;
            case 'weekly':
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($days as $day) {
                    $periods[$day] = [
                        'period' => $day,
                        'count' => 0,
                        'hours' => 0,
                        'revenue' => 0,
                    ];
                }
                break;
            case 'monthly':
                $daysInMonth = Carbon::parse($date)->daysInMonth;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $periods[(string)$day] = [
                        'period' => (string)$day,
                        'count' => 0,
                        'hours' => 0,
                        'revenue' => 0,
                    ];
                }
                break;
            case 'yearly':
                $months = ['January', 'February', 'March', 'April', 'May', 'June', 
                          'July', 'August', 'September', 'October', 'November', 'December'];
                foreach ($months as $month) {
                    $periods[$month] = [
                        'period' => $month,
                        'count' => 0,
                        'hours' => 0,
                        'revenue' => 0,
                    ];
                }
                break;
        }
        
        return $periods;
    }
    
    private function getPeriodInfo($type, $date)
    {
        $carbonDate = Carbon::parse($date);
        
        switch ($type) {
            case 'daily':
                return [
                    'start' => $carbonDate->format('Y-m-d'),
                    'end' => $carbonDate->format('Y-m-d'),
                    'label' => $carbonDate->format('F d, Y')
                ];
            case 'weekly':
                return [
                    'start' => $carbonDate->startOfWeek()->format('Y-m-d'),
                    'end' => $carbonDate->endOfWeek()->format('Y-m-d'),
                    'label' => 'Week of ' . $carbonDate->startOfWeek()->format('M d, Y')
                ];
            case 'monthly':
                return [
                    'start' => $carbonDate->startOfMonth()->format('Y-m-d'),
                    'end' => $carbonDate->endOfMonth()->format('Y-m-d'),
                    'label' => $carbonDate->format('F Y')
                ];
            case 'yearly':
                return [
                    'start' => $carbonDate->startOfYear()->format('Y-m-d'),
                    'end' => $carbonDate->endOfYear()->format('Y-m-d'),
                    'label' => $carbonDate->format('Y')
                ];
            default:
                return [
                    'start' => $carbonDate->format('Y-m-d'),
                    'end' => $carbonDate->format('Y-m-d'),
                    'label' => $carbonDate->format('F d, Y')
                ];
        }
    }
    
    public function export(Request $request)
    {
        $reportType = $request->get('type', 'daily');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $tenantId = $request->get('tenant_id');
        
        $reportData = $this->generateReport($reportType, $date, $tenantId);
        
        $filename = "report_{$reportType}_{$date}.csv";
        $handle = fopen('php://temp', 'w+');
        
        // Headers
        fputcsv($handle, ['Report Type', ucfirst($reportType)]);
        fputcsv($handle, ['Period', $reportData['period_info']['label']]);
        fputcsv($handle, ['Date Range', $reportData['period_info']['start'] . ' to ' . $reportData['period_info']['end']]);
        fputcsv($handle, []);
        
        // Summary
        fputcsv($handle, ['SUMMARY']);
        fputcsv($handle, ['Total Reservations', $reportData['summary']['total_reservations']]);
        fputcsv($handle, ['Total Hours', $reportData['summary']['total_hours']]);
        fputcsv($handle, ['Total Revenue', '$' . number_format($reportData['summary']['total_revenue'], 2)]);
        fputcsv($handle, []);
        
        // Breakdown
        fputcsv($handle, ['DETAILED BREAKDOWN']);
        fputcsv($handle, ['Period', 'Reservations', 'Hours', 'Revenue']);
        
        foreach ($reportData['grouped_data'] as $data) {
            fputcsv($handle, [
                $data['period'],
                $data['count'],
                $data['hours'],
                '$' . number_format($data['revenue'], 2)
            ]);
        }
        fputcsv($handle, []);
        
        // Top Courts
        fputcsv($handle, ['TOP PERFORMING COURTS']);
        fputcsv($handle, ['Court Name', 'Reservations', 'Revenue']);
        
        foreach ($reportData['top_courts'] as $court) {
            fputcsv($handle, [
                $court->court->name ?? 'N/A',
                $court->total_reservations,
                '$' . number_format($court->total_revenue, 2)
            ]);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
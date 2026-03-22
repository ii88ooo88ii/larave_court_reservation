@extends('layouts.admin')

@section('title', 'Reservation Calendar')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reservation Calendar</h1>
        <div>
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-list"></i> List View
            </a>
            <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Reservation
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Calendar for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Time</th>
                            @foreach($courts as $court)
                                <th>{{ $court->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $hours = range(6, 22); // 6 AM to 10 PM
                        @endphp
                        @foreach($hours as $hour)
                        <tr>
                            <td>{{ sprintf('%02d:00', $hour) }} - {{ sprintf('%02d:00', $hour + 1) }}</td>
                            @foreach($courts as $court)
                                @php
                                    $timeSlot = sprintf('%02d:00:00', $hour);
                                    $nextTimeSlot = sprintf('%02d:00:00', $hour + 1);
                                    $reservation = isset($reservations[$court->id]) 
                                        ? $reservations[$court->id]->first(function($r) use ($timeSlot, $nextTimeSlot) {
                                            return $r->start_time <= $timeSlot && $r->end_time >= $nextTimeSlot;
                                        }) 
                                        : null;
                                @endphp
                                <td class="{{ $reservation ? 'bg-success text-white' : '' }}">
                                    @if($reservation)
                                        <strong>{{ $reservation->reservation_code }}</strong><br>
                                        {{ $reservation->user->name }}<br>
                                        <small>{{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} - 
                                               {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}</small>
                                    @else
                                        Available
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
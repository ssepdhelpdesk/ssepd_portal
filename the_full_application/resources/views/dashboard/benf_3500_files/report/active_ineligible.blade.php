@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Pensioner Abstract – ₹3500 Category (Age 80+ & ≥80% Disability)</h2>

    <!-- Date Filter Form -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="from_date">From Date</label>
            <input type="date" name="from_date" id="from_date" value="{{ $from_date }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="to_date">To Date</label>
            <input type="date" name="to_date" id="to_date" value="{{ $to_date }}" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Sl No</th>
                <th>District</th>
                <th>Total Oldage</th>
                <th>Oldage Death</th>
                <th>Oldage Ineligible</th>
                <th>Oldage Active</th>
                <th>Total Disability</th>
                <th>Disability Death</th>
                <th>Disability Ineligible</th>
                <th>Disability Active</th>
                <th>Total Discontinued</th>
            </tr>
        </thead>
        <tbody>
            @foreach($final_data as $row)
                <tr>
                    <td>{{ $row['SlNo'] }}</td>
                    <td>{{ $row['District'] }}</td>
                    <td>{{ $row['TotalOldage'] }}</td>
                    <td>{{ $row['OldageDeath'] }}</td>
                    <td>{{ $row['OldageIneligible'] }}</td>
                    <td>{{ $row['OldageActive'] }}</td>
                    <td>{{ $row['TotalDisability'] }}</td>
                    <td>{{ $row['DisabilityDeath'] }}</td>
                    <td>{{ $row['DisabilityIneligible'] }}</td>
                    <td>{{ $row['DisabilityActive'] }}</td>
                    <td>{{ $row['TotalDiscontinued'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

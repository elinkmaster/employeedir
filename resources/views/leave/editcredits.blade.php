@extends('layouts.main')
@section('content')
    <style type="text/css">
        small.leave-success{
            color: green;
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="{{route('expanded.credits')}}" class="btn btn-primary" style="margin-bottom: 1rem;">Back</a>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Edit Leave Credit
                </div>
                <div class="panel panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="POST" action="{{ url('leave/credits') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>Employee Name</label>
                                    <p>{{ $employee->fullName2() }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Department</label>
                                    <p>{{ $employee->team_name }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Position</label>
                                    <p>{{ $employee->position_name }}</p>
                                </div>
                                <div class="form-group">
                                    <label>Remaining Leave Credits</label>
                                     <input type="number" class="form-control" disabled value="{{number_format($credits->current_credit, 2)}}">
				</div>
				<div class="form-group">
                                     <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ now()->subYear()->format('Y') }} PTO Forwarded</th>
                                                <th>{{ now()->format('Y') }} Monthly Accrual</th>
                                                <th>Used PTO<br>(Jan-Jun)</th>
                                                <th>Used PTO<br>(Jul-Dec)</th>
                                                <th>Current PTO Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    {{ number_format($credits->past_credit - $credits->conversion_credit, 2) }}
                                                </td>
                                                <td>{{ number_format($credits->monthly_accrual, 2) }}</td>
                                                <td>{{ number_format($credits->used_jan_to_jun, 2) }}</td>
                                                <td>{{ number_format($credits->used_jul_to_dec, 2) }}</td>
                                                <td>
                                                    {{ $credits->is_regular == 1 ? number_format($credits->current_credit, 2) + number_format($credits->past_credit - $credits->conversion_credit, 2) - (number_format($credits->used_jan_to_jun, 2) + number_format($credits->used_jul_to_dec, 2)) : '0.00' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="display: flex; gap: 1rem;">
                                    <div class="form-group">
                                        <label for="pto_forwarded">{{ now()->subYear()->format('Y') }} PTO
                                            Forwarded</label>
                                        <input type="number" name="pto_forwarded" id="pto_forwarded"
                                            step="0.01" value="0" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="monthly_accrual">{{ now()->format('Y') }} Monthly Accrual</label>
                                        <input type="number" name="monthly_accrual" id="monthly_accrual" 
                                            step="0.01" value="0" class="form-control">
                                    </div>
                                </div>
                                    <input type="hidden" class="form-control" value="{{ $employee->id }}" name="employee_id">
                                <button class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>

    </script>
@endsection

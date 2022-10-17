@extends('layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Leave Credits
                    @if(Auth::check())
                        @if(Auth::user()->isAdmin())
                        <!-- <a href="{{ url('download-credits') }}" class="pull-right btn btn-info"><span class="fa fa-gear"></span>&nbsp;Download Leave Credits</a>&nbsp; -->
                        <a href="{{ url('leave') }}" class="pull-right btn btn-primary"><span class="fa fa-gear"></span>&nbsp;View Leave Lists</a>&nbsp;
                        @endif
                    @endif
                </div>
                <div class="pane-body panel">
                    <br>
                    <br>
                    <table class="table table-striped" id="leave_credits_table">
                        <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th><?php echo date('Y') - 1 ?> PTO<br>Forwarded</th>
                            <th><?php echo date('Y') ?> PTO<br>Monthly Accrual</th>
                            <th>Used PTO<br>(Jan-Jun)</th>
                            <th>Used PTO<br>(Jul-Dec)</th>
                            <th>Current PTO Balance</th>
			    <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->eid }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ number_format($employee->past_credit - $employee->conversion_credit,2) }}</td>
                                <?php
                                    $div = 0;
                                    switch($employee->employee_category):
                                        case 1: $div = 20; break;
                                        case 2: $div = 14; break;
                                        case 3: $div = 10; break;
                                        case 4: $div = 10; break;
                                    endswitch;
				    $differentInMonths = App\Helpers\DateHelper::getDifferentMonths($employee->hired_date);
                                    $monthlyAccrual = $div/12 * $differentInMonths + $employee->monthly_accrual;
                                ?>
                                <td>{{ number_format($monthlyAccrual,2) }}</td>
                                <!-- <td <?php if(abs($employee->loa) > 0) { ?> style="color: red;"<?php } ?>>{{ abs(number_format($employee->loa,1)) }}</td> -->
                                <td>{{ number_format($employee->used_jan_to_jun,2) }}</td>
                                <?php
                                $pto_forwarded = $employee->past_credit - $employee->conversion_credit;
                                $pto_accrue = $employee->current_credit;
                                $loa = abs($employee->loa);
                                $use_jan_jun = $employee->used_jan_to_jun;
                                $pto_expired = $employee->expired_credit;
                                $balance = $pto_forwarded + $pto_accrue - $loa - $use_jan_jun - $pto_expired;
                                ?>
                                <td>{{ number_format($employee->used_jul_to_dec,2) }}</td>
                                <td>
 				    {{ $employee->is_regular == 1 ? number_format($employee->current_credit, 2) + number_format($employee->past_credit - $employee->conversion_credit, 2) - (number_format($employee->used_jan_to_jun, 2) + number_format($employee->used_jul_to_dec, 2)) : '0.00' }}
				</td>
				@if(Auth::user()->isAdmin())
                                    <td>
                                        <a title="Adjust leave credits" href="{{ url('leave/credits') . '/' . $employee->id }}"><i class="fa fa-gear"></i></a>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $('#leave_credits_table').dataTable({
            "pageLength": 50
        });
    </script>
@endsection

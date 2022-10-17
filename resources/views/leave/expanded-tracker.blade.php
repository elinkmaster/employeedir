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
                            <th><?php echo date('Y') - 1  ?> PTO Balance</th>
                            <th><?php echo date('Y') - 1  ?> PTO<br>Conversion</th>
                            <th><?php echo date('Y') - 1 ?> PTO<br>Forwarded</th>
                            <th><?php echo date('Y') ?> PTO<br>Monthly Accrual</th>
                            <th>LOA<br>Unearned Credits</th>
                            <th>Used PTO<br>(Jan-Jun)</th>
                            <th><?php echo date('Y') - 1  ?> PTO Expired</th>
                            <th>PTO Balance<br>(start July)</th>
                            <th>Used PTO<br>(Jul-Dec)</th>
                            <th>Current PTO Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->id }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ number_format($employee->past_credit,1) }}</td>
                                <td>{{ number_format($employee->conversion_credit,1) }}</td>
                                <td>{{ number_format($employee->past_credit - $employee->conversion_credit,1) }}</td>
                                <td>{{ number_format($employee->current_credit,1) }}</td>
                                <td <?php if(abs($employee->loa) > 0) { ?> style="color: red;"<?php } ?>>{{ abs(number_format($employee->loa,1)) }}</td>
                                <td>{{ number_format($employee->used_jan_to_jun,1) }}</td>
                                <td>{{ number_format($employee->expired_credit,1) }}</td>
                                <?php
                                $pto_forwarded = $employee->past_credit - $employee->conversion_credit;
                                $pto_accrue = $employee->current_credit;
                                $loa = abs($employee->loa);
                                $use_jan_jun = $employee->used_jan_to_jun;
                                $pto_expired = $employee->expired_credit;
                                $balance = $pto_forwarded + $pto_accrue - $loa - $use_jan_jun - $pto_expired;
                                ?>
                                <td>{{ number_format($balance,1) }}</td>
                                <td>{{ number_format($employee->used_jul_to_dec,1) }}</td>
                                <td>{{ $employee->is_regular == 1 ? number_format($balance - $employee->used_jul_to_dec,1) : 0.0 }}</td>
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
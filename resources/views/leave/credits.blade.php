@extends('layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Leave Credits
                    @if(Auth::check())
                        @if(Auth::user()->isAdmin())
                        <a href="{{ url('download-credits') }}" class="pull-right btn btn-info"><span class="fa fa-gear"></span>&nbsp;Download Leave Credits</a>&nbsp;
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
                            <th>Position</th>
                            <th><?php echo date('Y') - 1  ?> PTO<br> For Conversion</th>
                            <th><?php echo date('Y') - 1 ?> PTO<br>Expiry - June <?php echo date('Y') ?></th>
                            <th><?php echo date('Y') ?> PTO<br>Monthly Accrual</th>
                            <th>Used PTO</th>
                            <th>PTO Balance</th>
 			    <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->eid }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ $employee->position_name }}</td>
                                <td>{{ number_format($employee->conversion_credit,2) }}</td>
                                <td>{{ number_format($employee->past_credit - $employee->conversion_credit,2) }}</td>
                                <td>{{ number_format($employee->current_credit,2) }}</td>
                                <td>{{ number_format($employee->used_credit,2) }}</td>
                                <td>{{ number_format($employee->total_credits,2) }}</td>
                                
                                <td>
                                    <a title="Adjust leave credits" href="{{ url('leave/credits') . '/' . $employee->id }}"><i class="fa fa-gear"></i></a>
                                </td>
                                
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

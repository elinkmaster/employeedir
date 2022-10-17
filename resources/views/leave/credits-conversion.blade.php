@extends('layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Leave Credits
                    @if(Auth::check())
                        @if(Auth::user()->isAdmin())
                            <a href="{{ url('leave') }}" class="pull-right btn btn-primary"><span class="fa fa-gear"></span>View Leave Lists</a>
                        @endif
                    @endif
                </div>
                <div class="pane-body panel">
                    <br>
                    <br>
                    <table class="table table-striped" id="credit-conversion-table">
                        <thead>
                        <tr>
                            <td>Employee ID</td>
                            <td>Employee Name</td>
                            <td>Position</td>
                            <td><?php echo date('Y') - 1  ?> PTO For Conversion</td>
                            <td><?php echo date('Y') - 1 ?> PTO</td>
                            <td><?php echo date('Y') ?> PTO</td>
                            <td>Used PTO</td>
                            <td>PTO Balance</td>
                            <td>Action</td>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->eid }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ $employee->position_name }}</td>
                                <td>{{ number_format($employee->conversion_credit,1) }}</td>
                                <td>{{ number_format($employee->past_credit - $employee->conversion_credit,1) }}</td>
                                <td>{{ number_format($employee->current_credit,1) }}</td>
                                <td>{{ number_format($employee->used_credit,1) }}</td>
                                <td>{{ number_format($employee->total_credits,1) }}</td>
                                <td>
                                <?php
                                if($employee->conversion_credit == 0 && $employee->total_credits >= 5 ){
                                ?>
                                    <btn class="btn btn-info create-conversion" data-id="<?php echo $employee->id ?>">Conversion</btn>
                                <?php
                                }
                                ?>
                        
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="mdl_conversion" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create Conversion</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Credits for Conversion</label>
                        <input type="text" class="form-control" id="field_conversion" placeholder="Conversion">
                        <input type="hidden" id="employee_conversion" value="0">
                    </div>
                </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary save-conversion">Save Conversion</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
    
        $.ajaxPrefilter(function(options, originalOptions, jqXHR){
            if (options.type.toLowerCase() === "post") {
                options.data = options.data || "";
                options.data += options.data?"&":"";
                options.data += "_token=" + encodeURIComponent(csrf_token);
            }
        });
        
        $(function(){

        });
        
        $(".create-conversion").click(function(e){
            e.preventDefault();
            var val = $(this).data('id');
            $("#employee_conversion").val(val);
            console.log(val);
            $("#mdl_conversion").modal('show');
        });
        
        $(".save-conversion").click(function(e){
            e.preventDefault();
            $.post("/save-conversion",{con : $("#field_conversion").val(), id : $("#employee_conversion").val()},function(){
                location.reload();
            });
        });
    </script>
@endsection
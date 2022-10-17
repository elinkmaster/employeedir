@extends('layouts.main')
@section('title')
Linking Form - New
@endsection
@section('pagetitle')
Linking Form - Supervisor/Manager
@endsection
@section('content')
<div class="container-fluid">
    <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <form id="form_quick_link">
                <div class="row">
                    <div class="col-md-12"><b style="color: #0000FF; font-size: 16px;">Pending Quick Link - For Acknowledgement</b></div>
                </div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputDate" class="form-label">Date</label>
                            <input type="text" class="form-control" id="exampleInputDate" aria-describedby="Coaching Date" readonly="1" value="<?php echo date("F d, Y",strtotime($info->lnk_date)) ?>">
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="focus_Name" class="form-label">Focus</label>
                            <input type="text" name="rf_focus" value="<?php echo $info->fc_desc ?>" class="form-control" readonly="1">
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputDate" class="form-label">Comments</label>
                            <textarea name="rf_comments" rows="10" class="form-control" readonly="1"><?php echo $info->rf_comments ?></textarea>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputDate" class="form-label">Feedback</label>
                            <textarea name="rf_feedback" rows="10" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <br>
                            <input type="hidden" name="lnk_id" value="<?php echo $info->lnk_id ?>">
                            <input type="submit" name="acknowledge_linking" id="btn-process_submit" class="btn btn-lg btn-primary" value="ACKNOWLEDGE LINKING">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        initVals();
    });
    
    function initVals(){
    }
</script>
@endsection
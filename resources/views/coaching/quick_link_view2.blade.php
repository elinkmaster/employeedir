@extends('layouts.main')
@section('title')
View Linking
@endsection
@section('pagetitle')
View Linking
@endsection
@section('content')
<div class="container-fluid">
    <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <form id="form_quick_link">
                <div class="row">
                    <div class="col-md-12" style="padding: 12px;"><b style="color: #0000FF; font-size: 16px;">View Quick Link</b></div>
                </div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="lnk_linkee_name" class="form-label">Staff/Linkee</label>
                            <input type="text" class="form-control" id="lnk_linkee_name" aria-describedby="Staff/Linkee" readonly="1" value="<?php echo $lnk_linkee_name ?>">
                        </div>
                    </div>
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
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputDate" class="form-label">Feedback</label>
                            <textarea name="rf_feedback" rows="10" class="form-control" readonly="1"><?php echo $info->rf_feedback ?></textarea>
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
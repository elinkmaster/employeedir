@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12" style="padding: 12px;"><b style="color: #0000FF; font-size: 16px;">View/Update Quick Link</b></div>
            </div>
            <form id="form_quick_link">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="main_linkee" class="form-label">Staff/Linkee</label>
                            <select id="main_linkee" name="lnk_linkee" class="form-control select2" aria-label="Select a Linkee" aria-describedby="lnk_linkeeHelp">
                                <option value="0" selected>Select a Linkee</option>
                                <?php
                                $linkee = $lastVal['flag'] ? $lastVal['lnk_linkee'] : $obj->lnk_linkee;
                                foreach($names as $ss):
                                ?>
                                <option value="<?php echo $ss->id ?>" full_name="<?php echo $ss->first_name." ".$ss->last_name ?>"<?php echo $linkee == $ss->id ? " selected" : ""?>><?php echo $ss->last_name.", ".$ss->first_name ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                            <div id="lnk_linkeeHelp" class="form-text" style="color: red; display: none;">* Linkee is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputDate" class="form-label">Date</label>
                            <input type="text" class="form-control" id="exampleInputDate" aria-describedby="Coaching Date" readonly="1" value="<?php echo date("F d, Y",strtotime($obj->lnk_date)) ?>">
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="focus_Name" class="form-label">Focus</label>
                            <select id="focus_Name" name="rf_focus" class="form-control select2" aria-label="Select a Focus" aria-describedby="rf_focusHelp">
                                <option value="0" selected>Select a Focus</option>
                                <?php
                                $test_focus = $lastVal['flag'] ? $lastVal['rf_focus'] : $obj->rf_focus;
                                foreach($focus as $ss):
                                ?>
                                <option value="<?php echo $ss->fc_id ?>" <?php echo $test_focus == $ss->fc_id ? " selected" : ""?>><?php echo $ss->fc_desc ?></option>
                                <?php
                                endforeach;
                                $test_comment = $lastVal['flag'] ? $lastVal['rf_comments'] : $obj->rf_comments;
                                ?>
                            </select>
                            <div id="rf_focusHelp" class="form-text" style="color: red; display: none;">* Focus is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputDate" class="form-label">Comments</label>
                            <textarea name="rf_comments" rows="10" class="form-control"><?php echo $test_comment ?></textarea>
                            <div id="rf_commentsHelp" class="form-text" style="color: red; display: none;">* Comments is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <br>
                            <input type="hidden" name="lnk_linker" value="<?php echo $obj->lnk_linker ?>">
                            <input type="hidden" name="lnk_date" value="<?php echo $obj->lnk_date ?>">
                            <input type="hidden" name="lnk_type" value="1">
                            <input type="submit" name="update_linking" id="btn-process_submit" class="btn btn-lg btn-primary" value="UPDATE LINKING">
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
        var linkee = <?php echo $lastVal['lnk_linkee'] ? 1 : 0 ?>;
        var focus = <?php echo $lastVal['rf_focus'] ? 1 : 0 ?>;
        var comments = <?php echo $lastVal['rf_comments'] ? 1 : 0 ?>;
        var flag = <?php echo $lastVal['flag'] ? 1 : 0 ?>;
        
        if(flag && linkee == 0){
            $("#lnk_linkeeHelp").show();
        }
        
        if(flag && focus == 0){
            $("#rf_focusHelp").show();
        }
        
        if(flag && comments == 0){
            $("#rf_commentsHelp").show();
        }

    }
</script>
@endsection
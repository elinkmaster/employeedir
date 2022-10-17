@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <form id="form_quick_link" autocomplete="off">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="staffName" class="form-label">Staff</label>
                            <input type="text" class="form-control" id="staffName" name="lnk_linkee_name" aria-describedby="Staff" readonly="1" value="<?php echo $obj['lnk_linkee_name'] ?>">
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputDate" class="form-label">Date</label>
                            <input type="text" class="form-control" id="exampleInputDate" aria-describedby="Coaching Date" readonly="1" value="<?php echo $obj['lnk_date'] ?>">
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sda_type" class="form-label">SDA Type</label>
                            <select class="form-control" name="sda_type" id="sda_type" disabled="1">
                                <option value="0">Select SDA Type</option>
                                <option value="1" <?php echo $obj['sda_type'] == 1 ? 'selected="1"' : '' ?>>Call Listening Session</option>
                                <option value="2" <?php echo $obj['sda_type'] == 2 ? 'selected="1"' : '' ?>>Mock Calls</option>
                                <option value="3" <?php echo $obj['sda_type'] == 3 ? 'selected="1"' : '' ?>>Calibration Sessions</option>
                            </select>
                            <div id="sda_typeHelp" class="form-text" style="color: red; display: none;">*  This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sda_date_call" class="form-label">Date of Call</label>
                            <input type="text" name="sda_date_call" id="sda_date_call" class="form-control" value="<?php echo $obj['sda_date_call'] ?>" readonly="1">
                            <div id="sda_date_callHelp" class="form-text" style="color: red; display: none;">*  This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sda_call_sel" class="form-label">Call Selection</label>
                            <input type="text" name="sda_call_sel" id="sda_call_sel" class="form-control" value="<?php echo $obj['sda_call_sel'] ?>" readonly="1">
                            <div id="sda_call_selHelp" class="form-text" style="color: red; display: none;">*  This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sda_www_u_said" class="form-label">What Went Well, You Said</label>
                            <textarea id="sda_www_u_said" name="sda_www_u_said" rows="7" class="form-control" readonly="1"><?php echo $obj['sda_www_u_said'] ?></textarea>
                            <div id="sda_www_u_saidHelp" class="form-text" style="color: red; display: none;">*  This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sda_www_i_said" class="form-label">What Went Well, I Said</label>
                            <textarea id="sda_www_i_said" name="sda_www_i_said" rows="7" class="form-control" readonly="1"><?php echo $obj['sda_www_i_said'] ?></textarea>
                            <div id="sda_www_i_saidHelp" class="form-text" style="color: red; display: none;">*  This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sda_wcm_u_said" class="form-label">What Could Make This Even Better, You Said</label>
                            <textarea id="sda_wcm_u_said" name="sda_wcm_u_said" rows="7" class="form-control" readonly="1"><?php echo $obj['sda_wcm_u_said'] ?></textarea>
                            <div id="sda_wcm_u_saidHelp" class="form-text" style="color: red; display: none;">*  This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sda_wcm_i_said class="form-label">What Could Make this Even Better, I Said</label>
                            <textarea id="sda_www_i_said" name="sda_wcm_i_said" rows="7" class="form-control" readonly="1"><?php echo $obj['sda_wcm_i_said'] ?></textarea>
                            <div id="sda_wcm_i_saidHelp" class="form-text" style="color: red; display: none;">*  This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleSDAComments" class="form-label">Comments</label>
                            <textarea id="exampleSDAComments" name="sda_comments" rows="10" class="form-control" readonly="1"><?php echo $obj['sda_comments'] ?></textarea>
                            <div id="sda_commentsHelp" class="form-text" style="color: red; display: none;">* Comments are required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleSDAFeedback" class="form-label">Feedback</label>
                            <textarea id="exampleSDAFeedback" name="sda_feedback" rows="10" class="form-control" <?php echo $obj['view_only'] ? 'readonly="1"' :'' ?>><?php echo $obj['sda_feedback'] ?></textarea>
                            <div id="sda_feedbackHelp" class="form-text" style="color: red; display: none;">* Feedback is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <br>
                            <input type="hidden" name="lnk_linker" value="<?php echo $obj['lnk_linker'] ?>">
                            <input type="hidden" name="lnk_linker_email" value="<?php echo $obj['lnk_linker_email'] ?>">
                            <input type="hidden" name="lnk_linkee" value="<?php echo $obj['lnk_linkee'] ?>">
                            <input type="hidden" name="lnk_linkee_email" value="<?php echo $obj['lnk_linkee_email'] ?>">
                            <input type="hidden" name="lnk_date" value="<?php echo $obj['lnk_date'] ?>">
                            <input type="hidden" name="lnk_type" value="<?php echo $obj['lnk_type'] ?>">
                            <input type="hidden" name="update" value="<?php echo $obj['update'] ? 1 : 0 ?>">
                            <input type="hidden" name="sda_com_id" value="<?php echo $obj['sda_com_id'] ?>">
                            <?php
                            if($obj['view_only'] == 0):
                            ?>
                            <input type="submit" name="ack_sda_linking" id="btn-process_submit" class="btn btn-lg btn-primary" value="ACKNOWLEDGE SDA LINKING">
                            <?php
                            endif;
                            ?>
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
    
    function initVals(){ //se_when_use
          
        var flag = <?php echo $obj['flag'] ? 1 : 0 ?>;
        var sda_type = <?php echo $obj['sda_type'] ? 1 : 0 ?>;
        var sda_date_call = <?php echo $obj['sda_date_call'] ? 1 : 0 ?>;
        var sda_call_sel = <?php echo $obj['sda_call_sel'] ? 1 : 0 ?>;
        var sda_www_u_said = <?php echo $obj['sda_www_u_said'] ? 1 : 0 ?>;
        var sda_www_i_said = <?php echo $obj['sda_www_i_said'] ? 1 : 0 ?>;
        var sda_wcm_u_said = <?php echo $obj['sda_wcm_u_said'] ? 1 : 0 ?>;
        var sda_wcm_i_said = <?php echo $obj['sda_wcm_i_said'] ? 1 : 0 ?>;
        var sda_comments = <?php echo $obj['sda_comments'] ? 1 : 0 ?>;
        var sda_feedback = <?php echo $obj['sda_feedback'] ? 1 : 0 ?>;
        
        if(flag && sda_type == 0){
            $("#sda_typeHelp").show();
        }
        
        if(flag && sda_date_call == 0){
            $("#sda_date_callHelp").show();
        }
        
        if(flag && sda_call_sel == 0){
            $("#sda_call_selHelp").show();
        }
        
        if(flag && sda_www_u_said == 0){
            $("#sda_www_u_saidHelp").show();
        }
        
        if(flag && sda_www_i_said == 0){
            $("#sda_www_i_saidHelp").show();
        }
        
        if(flag && sda_wcm_u_said == 0){
            $("#sda_wcm_u_saidHelp").show();
        }
        
        if(flag && sda_wcm_i_said == 0){
            $("#sda_wcm_i_saidHelp").show();
        }
        
        if(flag && sda_comments == 0){
            $("#sda_commentsHelp").show();
        }
        
        //sda_typeHelp
    }
</script>
@endsection
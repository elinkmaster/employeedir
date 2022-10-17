@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12" style="padding: 12px;"><b style="color: #0000FF; font-size: 16px;"><?php echo $obj['ackn_acc'] ? "ACKNOWLEDGE" : "VIEW" ?> Accountability Setting Linking Session</b></div>
            </div>
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
                            <label for="focus_Name" class="form-label">Focus</label>
                            <select disabled="1" id="focus_Name" name="ac_focus" class="form-control select2" aria-label="Select a Focus" aria-describedby="rf_focusHelp">
                                <option value="0" selected>Select a Focus</option>
                                <?php
                                foreach($obj['sel_focus'] as $ss):
                                ?>
                                <option value="<?php echo $ss->fc_id ?>" <?php echo $obj['ac_focus'] == $ss->fc_id ? " selected" : ""?>><?php echo $ss->fc_desc ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                            <div id="se_focusHelp" class="form-text" style="color: red; display: none;">* Focus is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputSkill" class="form-label">Behavior/Skill</label>
                            <input type="text" readonly="1" name="ac_skill" class="form-control" value="<?php echo $obj['ac_skill'] ?>">
                            <div id="se_skillHelp" class="form-text" style="color: red; display: none;">* Behavior/Skill is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputSeWhenUse" class="form-label">When we use the Skill</label>
                            <input id="exampleInputSeWhenUse" readonly="1" type="text" name="ac_when_use" class="form-control" value="<?php echo $obj['ac_when_use'] ?>">
                            <div id="se_skillWhenHelp" class="form-text" style="color: red; display: none;">* This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputSeHowUse" class="form-label">How we use the Skill</label>
                            <input id="exampleInputSeHowUse" readonly="1" type="text" name="ac_how_use" class="form-control" value="<?php echo $obj['ac_how_use'] ?>">
                            <div id="se_skillHowHelp" class="form-text" style="color: red; display: none;">* This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputSeWhyUse" class="form-label">Why we use the Skill</label>
                            <input id="exampleInputSeWhyUse" readonly="1" type="text" name="ac_why_use" class="form-control" value="<?php echo $obj['ac_why_use'] ?>">
                            <div id="se_WhyUseHelp" class="form-text" style="color: red; display: none;">* This is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputSeMyExpect" class="form-label">My Expectations</label>
                            <input id="exampleInputSeMyExpect" readonly="1" type="text" name="ac_expectations" class="form-control" value="<?php echo $obj['ac_expectations'] ?>">
                            <div id="se_ExpectationsHelp" class="form-text" style="color: red; display: none;">* Expectation is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ac_expectation_date" class="form-label">Date Expectation was Set</label>
                            <input id="ac_expectation_date" readonly="1" type="text" name="ac_expectation_date" class="form-control" value="<?php echo $obj['ac_expectation_date'] ?>">
                            <div id="ac_expectation_dateHelp" class="form-text" style="color: red; display: none;">* Expectation Date is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleSEComments" class="form-label">Comments</label>
                            <textarea id="exampleSEComments" name="ac_comments" rows="10" class="form-control" readonly="1"><?php echo $obj['ac_comments'] ?></textarea>
                            <div id="rf_commentsHelp" class="form-text" style="color: red; display: none;">* Comments is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleSEComments" class="form-label">Feedback</label>
                            <textarea id="exampleSEComments"<?php echo $obj['view_accnt'] ? " readonly='1' " : "" ?> name="ac_feedback" rows="10" class="form-control"><?php echo $obj['ac_feedback'] ?></textarea>
                            <div id="ac_feedbackHelp" class="form-text" style="color: red; display: none;">* Feedback is required and necessary.</div>
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
                            <input type="hidden" name="update" value="<?php echo $obj['update'] ? 1 : 0 ?>">
                            <input type="hidden" name="ac_com_id" value="<?php echo $obj['ac_com_id'] ?>">
                            <input type="hidden" name="lnk_linkee" value="<?php echo $obj['lnk_linkee'] ?>">
                            <input type="hidden" name="lnk_linkee_email" value="<?php echo $obj['lnk_linkee_email'] ?>">
                            <input type="hidden" name="lnk_date" value="<?php echo $obj['lnk_date'] ?>">
                            <input type="hidden" name="lnk_type" value="<?php echo $obj['lnk_type'] ?>">
                            
                            <input type="submit"<?php echo $obj['view_accnt'] ? " style='display: none;' " : "" ?> name="acknw_ac_linking" id="btn-process_submit" class="btn btn-lg btn-primary" value="ACKNOWLEDGE ACCOUNTABILITY LINKING">
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
        $("#ac_expectation_date").datepicker({
            changeMonth : true,
            changeYear  : true
        });
        
        var focus = <?php echo $obj['ac_focus'] ? 1 : 0 ?>;
        var se_skill = <?php echo $obj['ac_skill'] ? 1 : 0 ?>;
        var se_when_use = <?php echo $obj['ac_when_use'] ? 1 : 0 ?>;
        var se_why_use = <?php echo $obj['ac_why_use'] ? 1 : 0 ?>;
        var se_expectations = <?php echo $obj['ac_expectations'] ? 1 : 0 ?>;
        var ac_expectation_date = <?php echo $obj['ac_expectation_date'] ? 1 : 0 ?>;
        var comments = <?php echo $obj['ac_comments'] ? 1 : 0 ?>;
        var feedback = <?php echo $obj['ac_feedback'] ? 1 : 0 ?>;
        var flag = <?php echo $obj['flag'] ? 1 : 0 ?>;
        
        if(flag && se_skill == 0){
            $("#se_skillHelp").show();
        }
    
        if(flag && se_when_use == 0){
            $("#se_skillWhenHelp").show();
        }
        
        if(flag && se_how_use == 0){
            $("#se_skillHowHelp").show();
        }
        
        if(flag && se_why_use == 0){
            $("#se_WhyUseHelp").show();
        }
        
        if(flag && se_expectations == 0){
            $("#se_ExpectationsHelp").show();
        }

        if(flag && ac_expectation_date == 0){
            $("#ac_expectation_dateHelp").show();
        }
    
        if(flag && focus == 0){
            $("#se_focusHelp").show();
        }
        
        if(flag && comments == 0){
            $("#rf_commentsHelp").show();
        }
        
        if(flag && feedback ==0){
            $("#ac_feedbackHelp").show();
        }
        
        console.log({focus : focus, comments : comments, flag : flag});

    }
</script>
@endsection
@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <form id="form_quick_link">
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
                            <input type="text" readonly="1" class="form-control" value="<?php echo $obj['focus_desc'] ?>">
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
                            <input type="text" name="se_skill" readonly="1" class="form-control" value="<?php echo $obj['se_skill'] ?>">
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
                            <input id="exampleInputSeWhenUse" readonly="1" type="text" name="se_when_use" class="form-control" value="<?php echo $obj['se_when_use'] ?>">
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
                            <input id="exampleInputSeHowUse" readonly="1" type="text" name="se_how_use" class="form-control" value="<?php echo $obj['se_how_use'] ?>">
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
                            <input id="exampleInputSeWhyUse" readonly="1" type="text" name="se_why_use" class="form-control" value="<?php echo $obj['se_why_use'] ?>">
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
                            <input id="exampleInputSeMyExpect" readonly="1" type="text" name="se_expectations" class="form-control" value="<?php echo $obj['se_expectations'] ?>">
                            <div id="se_ExpectationsHelp" class="form-text" style="color: red; display: none;">* Expectation is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <div>&nbsp;</div>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleSEComments" class="form-label">Comments</label>
                            <textarea id="exampleSEComments" name="se_comments" rows="10" class="form-control" readonly="1"><?php echo $obj['se_comments'] ?></textarea>
                            <div id="rf_commentsHelp" class="form-text" style="color: red; display: none;">* Comments is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleSEFeedback" class="form-label">Feedback</label>
                            <textarea id="exampleSEFeedback" readonly="1" name="se_feedback" rows="10" class="form-control"><?php echo $obj['se_feedback'] ?></textarea>
                            <div id="rf_commentsHelp" class="form-text" style="color: red; display: none;">* Comments is required and necessary.</div>
                        </div>
                    </div>
                </div>
                <br><br>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <input type="hidden" name="lnk_linker" value="<?php echo $obj['lnk_linker'] ?>">
                            <input type="hidden" name="update" value="0">
                            <input type="hidden" name="se_com_id" value="<?php echo $obj['se_com_id'] ?>">
                            <input type="hidden" name="lnk_linkee" value="<?php echo $obj['lnk_linkee'] ?>">
                            <input type="hidden" name="lnk_date" value="<?php echo $obj['lnk_date'] ?>">
                            <input type="hidden" name="lnk_type" value="<?php echo $obj['lnk_type'] ?>">
                            <input type="hidden" name="lnk_linker_email" value="<?php echo $obj['lnk_linker_email'] ?>">
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
        var focus = <?php echo $obj['se_focus'] ? 1 : 0 ?>;
        var se_skill = <?php echo $obj['se_skill'] ? 1 : 0 ?>;
        var se_when_use = <?php echo $obj['se_when_use'] ? 1 : 0 ?>;
        var se_how_use = <?php echo $obj['se_how_use'] ? 1 : 0 ?>;
        var se_why_use = <?php echo $obj['se_why_use'] ? 1 : 0 ?>;
        var se_expectations = <?php echo $obj['se_expectations'] ? 1 : 0 ?>;
        var comments = <?php echo $obj['se_comments'] ? 1 : 0 ?>;
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
    
        if(flag && focus == 0){
            $("#se_focusHelp").show();
        }
        
        if(flag && comments == 0){
            $("#rf_commentsHelp").show();
        }
        
        console.log({focus : focus, comments : comments, flag : flag});

    }
</script>
@endsection
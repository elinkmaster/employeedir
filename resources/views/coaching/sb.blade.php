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
            <div class="row">
                <div class="col-md-12"><b style="color: #0000FF; font-size: 16px;">New Skill Building Session</b></div>
            </div>
            <div class="row">
                <div class="col-md-6"><!-- Left Panel Begin -->
                    <form id="form_quick_link" autocomplete="off">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="staffName" class="form-label">Staff</label>
                                    <input type="text" class="form-control" id="staffName" name="lnk_linkee_name" aria-describedby="Staff" readonly="1" value="<?php echo $obj['lnk_linkee_name'] ?>">
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="exampleInputDate" class="form-label">Date</label>
                                    <input type="text" class="form-control" id="exampleInputDate" aria-describedby="Coaching Date" readonly="1" value="<?php echo $obj['lnk_date'] ?>">
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="focus_Name" class="form-label">Focus</label>
                                    <select id="focus_Name" name="sb_focus" class="form-control select2" aria-label="Select a Focus" aria-describedby="rf_focusHelp">
                                        <option value="0" selected>Select a Focus</option>
                                        <?php
                                        foreach($obj['sel_focus'] as $ss):
                                        ?>
                                        <option value="<?php echo $ss->fc_id ?>" <?php echo $obj['sb_focus'] == $ss->fc_id ? " selected" : ""?>><?php echo $ss->fc_desc ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <div id="sb_focusHelp" class="form-text" style="color: red; display: none;">* Focus is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="exampleInputDate" class="form-label">Behavior/Skill</label>
                                    <input name="sb_skill" type="text" class="form-control"  aria-describedby="Behavior Skill" value="<?php echo $obj['sb_skill'] ?>">
                                    <div id="sb_skillHelp" class="form-text" style="color: red; display: none;">* Behavior Skill is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="exampleInputDate" class="form-label">When we use the Skill</label>
                                    <input name="sb_when_skill" type="text" class="form-control"  aria-describedby="Behavior Skill" value="<?php echo $obj['sb_when_skill'] ?>">
                                    <div id="sb_when_skillHelp" class="form-text" style="color: red; display: none;">* This is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="exampleInputDate" class="form-label">How we use the Skill</label>
                                    <input name="sb_how_skill" type="text" class="form-control"  aria-describedby="Behavior Skill" value="<?php echo $obj['sb_how_skill'] ?>">
                                    <div id="sb_how_skillHelp" class="form-text" style="color: red; display: none;">* This is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="exampleInputDate" class="form-label">Why we use the Skill</label>
                                    <input name="sb_why_skill" type="text" class="form-control"  aria-describedby="Behavior Skill" value="<?php echo $obj['sb_why_skill'] ?>">
                                    <div id="sb_why_skillHelp" class="form-text" style="color: red; display: none;">* This is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="exampleInputDate" class="form-label">Takeaways from activity</label>
                                    <textarea name="sb_takeaway" rows="10" class="form-control"><?php echo $obj['sb_takeaway'] ?></textarea>
                                    <div id="sb_takeawayHelp" class="form-text" style="color: red; display: none;">* Comments is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="exampleInputDate" class="form-label">Timeframe for Quick Link</label>
                                    <input name="sb_timeframe" type="text" class="form-control"  aria-describedby="TimeFrame" value="<?php echo $obj['sb_timeframe'] ?>">
                                    <div id="sb_timeframeHelp" class="form-text" style="color: red; display: none;">* Timeframe is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <br>
                                    <input type="hidden" name="sb_com_num" value="<?php echo $obj['sb_com_num'] ?>">
                                    <input type="hidden" name="lnk_linker" value="<?php echo $obj['lnk_linker'] ?>">
                                    <input type="hidden" name="lnk_linkee" value="<?php echo $obj['lnk_linkee'] ?>">
                                    <input type="hidden" name="lnk_linkee_email" value="<?php echo $obj['lnk_linkee_email'] ?>">
                                    <input type="hidden" name="lnk_date" value="<?php echo $obj['lnk_date'] ?>">
                                    <input type="hidden" name="lnk_type" value="<?php echo $obj['lnk_type'] ?>">
                                    <input type="hidden" name="lnk_linker_email" value="<?php echo $obj['lnk_linker_email'] ?>">
                                    <input type="submit" name="save_SB_linking" id="btn-process_submit" class="btn btn-lg btn-primary" value="SAVE SKILL BUILDING SESSION">
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- Left Panel End -->
                <div class="col-md-6"><!-- Right Panel Begin -->
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>Date</th>
                            <th>Linker</th>
                            <th>Focus</th>
                            <th>Status</th>
                            <th>View Coaching</th>
                        </tr>
                        <?php
                        foreach($obj['linkee_listing'] as $lk):
                        ?>
                        <tr>
                            <td><?php echo date("F d, Y", strtotime($lk->lnk_date)) ?></td>
                            <td><?php echo $lk->linker ?></td>
                            <td><?php echo $lk->sb_focus ?></td>
                            <td><?php echo $lk->status ?></td>
                            <td><a href="<?php echo $lk->link ?>" class="link-primary" target="_blank">View</a> </td>
                        </tr>
                        <?php    
                        endforeach;
                        ?>
                    </table>
                </div><!-- Right Panel End -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        initVals();
    });
    
    function initVals(){
        var focus = <?php echo $obj['sb_focus'] ? 1 : 0 ?>;
        var sb_takeaway = <?php echo $obj['sb_takeaway'] ? 1 : 0 ?>;
        var sb_skill = <?php echo $obj['sb_skill'] ? 1 : 0 ?>;
        var sb_timeframe = <?php echo $obj['sb_timeframe'] ? 1 : 0 ?>;
        var sb_when_skill = <?php echo $obj['sb_when_skill'] ? 1 : 0 ?>;
        var sb_how_skill = <?php echo $obj['sb_how_skill'] ? 1 : 0 ?>;
        var sb_why_skill = <?php echo $obj['sb_why_skill'] ? 1 : 0 ?>;
        var flag = <?php echo $obj['flag'] ? 1 : 0 ?>;
        
        if(flag && focus == 0){
            $("#sb_focusHelp").show();
        }
        
        if(flag && sb_takeaway == 0){
            $("#sb_takeawayHelp").show();
        }
        
        if(flag && sb_skill == 0){
            $("#sb_skillHelp").show();
        }
        
        if(flag && sb_timeframe == 0){
            $("#sb_timeframeHelp").show();
        }
        
        if(flag && sb_when_skill == 0){
            $("#sb_when_skillHelp").show();
        }
        
        if(flag && sb_how_skill == 0){
            $("#sb_how_skillHelp").show();
        }
        
        if(flag && sb_why_skill == 0){
            $("#sb_why_skillHelp").show();
        }
        
        console.log({focus : focus, comments : comments, flag : flag});

    }
</script>
@endsection
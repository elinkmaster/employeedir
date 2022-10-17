@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12" style="padding: 12px;"><b style="color: #0000FF; font-size: 16px;"><?php echo $obj['update'] ? "VIEW/UPDATE" : "NEW" ?> Goal Setting Session</b></div>
            </div>
            <div class="row">
                <div class="col-md-7"><!-- Left Panel -->
                    <form id="form_gtky_session" autocomplete="off">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <label for="staffName" class="form-label">Staff</label>
                                    <input type="text" class="form-control" id="staffName" name="lnk_linkee_name" aria-describedby="Staff" readonly="1" value="<?php echo $obj['lnk_linkee_name'] ?>">
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <label for="exampleInputDate" class="form-label">Date</label>
                                    <input type="text" class="form-control" id="exampleInputDate" aria-describedby="Coaching Date" readonly="1" value="<?php echo $obj['lnk_date'] ?>">
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <label for="exampleInputSkill" class="form-label">Accomplishments</label>
                                    <input type="text" name="gs_accmpl" class="form-control" value="<?php echo $obj['gs_accmpl'] ?>">
                                    <div id="gs_accmplHelp" class="form-text" style="color: red; display: none;">* Accomplishment is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Num</th>
                                        <th>Metric</th>
                                        <th>Target</th>
                                        <th>Prev Month</th>
                                        <th>Cur Month</th>
                                    </tr>
                                    <tr>
                                        <th>1</th>
                                        <td>
                                            <input id="Input_gtk_bday" type="text" name="gs_metric_01" class="form-control" value="<?php echo $obj['gs_metric_01'] ?>">
                                            <div id="gs_metric_01Help" class="form-text" style="color: red; display: none;">* Required</div>
                                        </td>
                                        <td>
                                            <input id="exampleInputSeHowUse" type="text" name="gs_target_01" class="form-control" value="<?php echo $obj['gs_target_01'] ?>">
                                            <div id="gs_target_01Help" class="form-text" style="color: red; display: none;">* Required</div>
                                        </td>
                                        <td>
                                            <input id="exampleInputSeWhyUse" type="text" name="gs_prev_01" class="form-control" value="<?php echo $obj['gs_prev_01'] ?>">
                                            <div id="gs_prev_01Help" class="form-text" style="color: red; display: none;">* Required</div>
                                        </td>
                                        <td>
                                            <input id="exampleInputSeMyExpect" type="text" name="gs_curr_01" class="form-control" value="<?php echo $obj['gs_curr_01'] ?>">
                                            <div id="gs_curr_01Help" class="form-text" style="color: red; display: none;">* Required</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_metric_02" class="form-control" value="<?php echo $obj['gs_metric_02'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_target_02" class="form-control" value="<?php echo $obj['gs_target_02'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_prev_02" class="form-control" value="<?php echo $obj['gs_prev_02'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_curr_02" class="form-control" value="<?php echo $obj['gs_curr_02'] ?>"></td>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_metric_03" class="form-control" value="<?php echo $obj['gs_metric_03'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_target_03" class="form-control" value="<?php echo $obj['gs_target_03'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_prev_03" class="form-control" value="<?php echo $obj['gs_prev_03'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_curr_03" class="form-control" value="<?php echo $obj['gs_curr_03'] ?>"></td>
                                    </tr>
                                    <tr>
                                        <th>4</th>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_metric_04" class="form-control" value="<?php echo $obj['gs_metric_04'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_target_04" class="form-control" value="<?php echo $obj['gs_target_04'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_prev_04" class="form-control" value="<?php echo $obj['gs_prev_04'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_curr_04" class="form-control" value="<?php echo $obj['gs_curr_04'] ?>"></td>
                                    </tr>
                                    <tr>
                                        <th>5</th>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_metric_05" class="form-control" value="<?php echo $obj['gs_metric_05'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_target_05" class="form-control" value="<?php echo $obj['gs_target_05'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_prev_05" class="form-control" value="<?php echo $obj['gs_prev_05'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_curr_05" class="form-control" value="<?php echo $obj['gs_curr_05'] ?>"></td>
                                    </tr>
                                    <tr>
                                        <th>6</th>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_metric_06" class="form-control" value="<?php echo $obj['gs_metric_06'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_target_06" class="form-control" value="<?php echo $obj['gs_target_06'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_prev_06" class="form-control" value="<?php echo $obj['gs_prev_06'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_curr_06" class="form-control" value="<?php echo $obj['gs_curr_06'] ?>"></td>
                                    </tr>
                                    <tr>
                                        <th>7</th>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_metric_07" class="form-control" value="<?php echo $obj['gs_metric_07'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_target_07" class="form-control" value="<?php echo $obj['gs_target_07'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_prev_07" class="form-control" value="<?php echo $obj['gs_prev_07'] ?>"></td>
                                        <td><input id="Input_gtk_bday" type="text" name="gs_curr_07" class="form-control" value="<?php echo $obj['gs_curr_07'] ?>"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <label for="ac_expectation_date" class="form-label">Tip for the Month</label>
                                    <textarea id="exampleSEComments" name="gs_tip" rows="3" class="form-control"><?php echo $obj['gs_tip'] ?></textarea>
                                    <div id="gs_tipHelp" class="form-text" style="color: red; display: none;">* Tip for the Month is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <label for="exampleSEComments" class="form-label">Commitment</label>
                                    <textarea id="exampleSEComments" name="gs_com" rows="3" class="form-control"><?php echo $obj['gs_com'] ?></textarea>
                                    <div id="gs_comHelp" class="form-text" style="color: red; display: none;">* Commitment is required and necessary.</div>
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
                                    <input type="hidden" name="gs_com_id" value="<?php echo $obj['gs_com_id'] ?>">
                                    <input type="hidden" name="lnk_linkee" value="<?php echo $obj['lnk_linkee'] ?>">
                                    <input type="hidden" name="lnk_linkee_email" value="<?php echo $obj['lnk_linkee_email'] ?>">
                                    <input type="hidden" name="lnk_date" value="<?php echo $obj['lnk_date'] ?>">
                                    <input type="hidden" name="lnk_type" value="<?php echo $obj['lnk_type'] ?>">

                                    <input type="submit" name="save_goal_setting_session" id="btn-process_submit" class="btn btn-lg btn-primary" value="SAVE GOAL SETTING SESSION">
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- Left Panel End-->
                <div class="col-md-5"><!-- Right Panel -->
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>Date</th>
                            <th>Linker</th>
                            <th>Commitment</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                        <?php
                        foreach($obj['linkee_listing'] as $lk):
                        ?>
                        <tr>
                            <td><?php echo date("F d, Y", strtotime($lk->lnk_date)) ?></td>
                            <td><?php echo $lk->linker ?></td>
                            <td><?php echo $lk->gs_com ?></td>
                            <td><?php echo $lk->status ?></td>
                            <td><a href="<?php echo $lk->link ?>" class="link-primary" target="_blank">View</a> </td>
                        </tr>
                        <?php    
                        endforeach;
                        ?>
                    </table>
                </div><!-- Right Panel -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        initVals();
    });
    
    function initVals(){ //se_when_use
        
        var gs_accmpl       = <?php echo $obj['gs_accmpl'] ? 1 : 0 ?>;
        var gs_metric_01    = <?php echo $obj['gs_metric_01'] ? 1 : 0 ?>;
        var gs_target_01    = <?php echo $obj['gs_target_01'] ? 1 : 0 ?>;
        var gs_prev_01      = <?php echo $obj['gs_prev_01'] ? 1 : 0 ?>;
        var gs_curr_01      = <?php echo $obj['gs_curr_01'] ? 1 : 0 ?>;
        var gs_tip          = <?php echo $obj['gs_tip'] ? 1 : 0 ?>;
        var gs_com          = <?php echo $obj['gs_com'] ? 1 : 0 ?>;        
        var flag            = <?php echo $obj['flag'] ? 1 : 0 ?>;
        
        if(flag && gs_accmpl == 0){
            $("#gs_accmplHelp").show();
        }
    
        if(flag && gs_metric_01 == 0){
            $("#gs_metric_01Help").show();
        }
        
        if(flag && gs_target_01 == 0){
            $("#gs_target_01Help").show();
        }
        
        if(flag && gs_prev_01 == 0){
            $("#gs_prev_01Help").show();
        }
        
        if(flag && gs_curr_01 == 0){
            $("#gs_curr_01Help").show();
        }

        if(flag && gs_tip == 0){
            $("#gs_tipHelp").show();
        }
    
        if(flag && gs_com == 0){
            $("#gs_comHelp").show();
        }
                
    }
</script>
@endsection
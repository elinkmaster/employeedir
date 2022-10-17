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
                <div class="col-md-12"><b style="color: #0000FF; font-size: 16px;">New Quick Link Session</b></div>
            </div>
            <div class="row">
                <div class="col-md-6"><!-- Left Panel Begin -->
                    <form id="form_quick_link">
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
                                    <select id="focus_Name" name="rf_focus" class="form-control select2" aria-label="Select a Focus" aria-describedby="rf_focusHelp">
                                        <option value="0" selected>Select a Focus</option>
                                        <?php
                                        foreach($obj['sel_focus'] as $ss):
                                        ?>
                                        <option value="<?php echo $ss->fc_id ?>" <?php echo $obj['rf_focus'] == $ss->fc_id ? " selected" : ""?>><?php echo $ss->fc_desc ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <div id="rf_focusHelp" class="form-text" style="color: red; display: none;">* Focus is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="mb-3">
                                    <label for="exampleInputDate" class="form-label">Comments</label>
                                    <textarea name="rf_comments" rows="10" class="form-control"><?php echo $obj['rf_comments'] ?></textarea>
                                    <div id="rf_commentsHelp" class="form-text" style="color: red; display: none;">* Comments is required and necessary.</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <br>
                                    <input type="hidden" name="lnk_linker" value="<?php echo $obj['lnk_linker'] ?>">
                                    <input type="hidden" name="lnk_linkee" value="<?php echo $obj['lnk_linkee'] ?>">
                                    <input type="hidden" name="lnk_linkee_email" value="<?php echo $obj['lnk_linkee_email'] ?>">
                                    <input type="hidden" name="lnk_date" value="<?php echo $obj['lnk_date'] ?>">
                                    <input type="hidden" name="lnk_type" value="<?php echo $obj['lnk_type'] ?>">
                                    <input type="hidden" name="lnk_linker_email" value="<?php echo $obj['lnk_linker_email'] ?>">
                                    <input type="submit" name="save_linking" id="btn-process_submit" class="btn btn-lg btn-primary" value="SAVE LINKING">
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
                            <td><?php echo $lk->rf_focus ?></td>
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
        var focus = <?php echo $obj['rf_focus'] ? 1 : 0 ?>;
        var comments = <?php echo $obj['rf_comments'] ? 1 : 0 ?>;
        var flag = <?php echo $obj['flag'] ? 1 : 0 ?>;
        
        if(flag && focus == 0){
            $("#rf_focusHelp").show();
            console.log("show focus");
        }
        
        if(flag && comments == 0){
            $("#rf_commentsHelp").show();
            console.log("show comments");
        }
        
        console.log({focus : focus, comments : comments, flag : flag});

    }
</script>
@endsection
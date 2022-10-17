@extends('layouts.main')
@section('content')
    <style>
        .dates_info{color: black; border:none;}
        th{text-align: center; vertical-align: bottom;}
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Leave Tracker Reports &nbsp; - &nbsp;&nbsp;&nbsp; 
                    From:&nbsp;&nbsp;<input id="startdate" type="text" class="dates_info" autocomplete="off"> &nbsp;&nbsp;&nbsp; 
                    To:&nbsp;&nbsp;<input id="enddate" type="text" class="dates_info" autocomplete="off">
                    &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="pay_type" value="monthly"> <label for="male">Monthly</label>
                    &nbsp;&nbsp;<input type="radio" name="pay_type" value="weekly"> <label for="male">Weekly</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;<button id="id_btn_display" class="btn btn-md btn-primary">Display Report</button>
                    &nbsp;
                    <?php
                    if(isset($target)):
                    ?>
                    <a href="/download-report?from=<?php echo $target['from'] ?>&to=<?php echo $target['to'] ?>&type=<?php echo $target['type'] ?>" class="btn btn-md btn-success">Download</a>
                    <?php
                    else:
                    ?>
                    <a href="#" class="btn btn-md btn-success">Download</a>
                    <?php
                    endif;
                    ?>
                    
                </div>
                <div class="pane-body panel">
                    <br>
                    <br>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Leave ID</th>
                            <th>EE Number</th>
                            <th>EE Name</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>VL</th>
                            <th>SL</th>
                            <th>EL</th>
                            <th>VLWOP</th>
                            <th>SLWOP</th>
                            <th>ELWOP</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(isset($obj)):
                            $leave_id = isset($obj[0]) ? $obj[0]->leave_id : 0;
                            $ename = isset($obj[0]) ? $obj[0]->emp_name : '';
                            $eid = isset($obj[0]) ? $obj[0]->eid : 0;
                            $l_id = isset($obj[0]) ? $obj[0]->leave_id : '';
                            $date_filed = isset($obj[0]) ? $obj[0]->date_filed : '';
                            $start = isset($obj[0]) ? $obj[0]->date : '';
                            $stop = isset($obj[0]) ? $obj[0]->date : '';
                            $track = 0;
                            $vl = 0;
                            $sl = 0;
                            $el = 0;
                            $vlwop = 0;
                            $slwop = 0;
                            $elwop = 0;
                            foreach($obj as $o):
                                if($leave_id == $o->leave_id):
                                
                                else:
                                ?>
                            <tr>
                                <td><?php echo str_pad($l_id,5,"0",STR_PAD_LEFT) ?></td>
                                <td><?php echo $eid ?></td>
                                <td><?php echo $ename ?></td>
                                <td><?php echo date("F d, Y", strtotime($start)) ?></td>
                                <td><?php echo date("F d, Y", strtotime($stop)) ?></td>
                                <td><?php echo $vl ?></td>
                                <td><?php echo $sl ?></td>
                                <td><?php echo $el ?></td>
                                <td><?php echo $vlwop ?></td>
                                <td><?php echo $slwop ?></td>
                                <td><?php echo $elwop ?></td>
                            </tr>
                                <?php
                                    $vl = 0;
                                    $sl = 0;
                                    $el = 0;
                                    $vlwop = 0;
                                    $slwop = 0;
                                    $elwop = 0;
                                    $leave_id = $o->leave_id;
                                    $ename = $o->emp_name;
                                    $eid = $o->eid;
                                    $l_id = $o->leave_id;
                                    $date_filed = $o->date_filed;
                                    $start = $o->date;
                                endif;
                                switch($o->leave_type_id):
                                    case 4: $o->pay_type == 1 ? $sl+=$o->length : $slwop+=$o->length; break;
                                    case 5: $o->pay_type == 1 ? $vl+=$o->length : $vlwop+=$o->length; break;
                                    case 6: $o->pay_type == 1 ? $el+=$o->length : $elwop+=$o->length; break;
                                endswitch;
                                $stop = $o->date;
                            endforeach;
                            if($l_id){
                        ?>
                            <tr>
                                <td><?php echo str_pad($l_id,5,"0",STR_PAD_LEFT) ?></td>
                                <td><?php echo $eid ?></td>
                                <td><?php echo $ename ?></td>
                                <td><?php echo date("F d, Y", strtotime($start)) ?></td>
                                <td><?php echo date("F d, Y", strtotime($stop)) ?></td>
                                <td><?php echo $vl ?></td>
                                <td><?php echo $sl ?></td>
                                <td><?php echo $el ?></td>
                                <td><?php echo $vlwop ?></td>
                                <td><?php echo $slwop ?></td>
                                <td><?php echo $elwop ?></td>
                            </tr>
                        <?php
                            }
                        endif;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script type="text/javascript">
        $( function() {
            $( ".dates_info" ).datepicker({
                changeMonth: true
            });
        } );
        
        $("#id_btn_display").click(function(e){
            e.preventDefault();
            
            var val = {
                from    : $("#startdate").val(),
                to      : $("#enddate").val(),
                type    : $('input[name="pay_type"]:checked').val()
            };
            
            console.log(val);
            location.href = "/display-report?from=" + val.from + "&to=" + val.to + "&type=" + val.type;
        })
        
    </script>
@endsection
@extends('layouts.main')
@section('title')
View Profile
@endsection
@section('pagetitle')
Employee Break Information
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php
            foreach($types as $t):
                if($t['status'] == 1 ):
            ?>
                <div>
                    <button data-id="<?php echo $t['id'] ?>" data-status="<?php echo $t['status']; ?>" type="button" class="btn btn-block btn-info timer_s"><?php echo $t['desc'] ?></button>
                </div>
            <?php    
                endif;
            endforeach;
            ?>
            <button data-id="0" data-status="1" type="button" class="btn btn-block btn-dark timer_s">TIME OUT</button>
        </div>
        <div class="col-md-7">
            <div class="row">
                <div class="col-xs-3 border border-info" style="font-weight: bold; text-align: left;">Break Type</div>
                <div class="col-xs-4 border border-info" style="font-weight: bold; text-align: left;">Time Start</div>
                <div class="col-xs-4 border border-info" style="font-weight: bold; text-align: left;">Time Stop</div>
            </div>
            <?php
            /*
             * Buttons are only for IN and it will not be clickable
             * Must click to other button to start another logging
             * Remove the IN on the buttons.
             * Add Clock Out Button - end if shift
             * 
             * For the Logging, Add PC-Name and IP Address
            */
            foreach($breaks as $b):
            if(is_null($b->time_stop)){
                $first = date_create($b->time_start);
                $obj = DB::select("select now() as curr_time");
                $second = date_create(date("Y-m-d H:i:s",strtotime($obj[0]->curr_time)));
                $diff = date_diff($first,$second);
                $num_secs = $diff->format('%H') * 3600 + $diff->format('%I') * 60 + $diff->format('%S');
            }
            ?>
            <div class="row">
                <div class="col-xs-3<?php echo $b->bi_status == 1 ? " border-dark" : " border-info text-info" ?>"><label><?php echo $b->break_name ?></label></div>
                <div class="col-xs-4<?php echo $b->bi_status == 1 ? " border-dark" : " border-info text-info" ?>"><label><?php echo date("M d Y - h:i:s A", strtotime($b->time_start)) ?></label></div>
                <div class="col-xs-4<?php echo $b->bi_status == 1 ? " border-dark" : " border-info text-info" ?>"><?php echo is_null($b->time_stop) ? "On going - <label id='_".$b->bi_id."_hours'>".$diff->format( '%H' )."</label>:<label id='_".$b->bi_id."_minutes'>".$diff->format( '%I' )."</label>:<label id='_".$b->bi_id."_seconds'>".$diff->format( '%S' )."</label>" : "<label>".date("M d Y - h:i:s A", strtotime($b->time_stop))."</label>" ?></div>
            </div>
            <?php
            if(is_null($b->time_stop)):
            ?>
            <script type="text/javascript">
                var _<?php echo $b->bi_id ?>_secondsInfo = document.getElementById("_<?php echo $b->bi_id ?>_seconds");
                var _<?php echo $b->bi_id ?>_totalSeconds = <?php echo $num_secs ?>;
                setInterval(function(){
                    ++_<?php echo $b->bi_id ?>_totalSeconds; 
                    var hours = _<?php echo $b->bi_id ?>_totalSeconds / 3600;
                    var full_hours = parseInt(hours);
                    hours = hours - full_hours;
                    
                    _<?php echo $b->bi_id ?>_hours.innerHTML = pad(full_hours);
                    
                    var minutes = hours * 60;
                    var full_minutes = parseInt(minutes);
                    minutes = minutes - full_minutes;
                    _<?php echo $b->bi_id ?>_minutes.innerHTML = pad(full_minutes);
                    
                    var seconds = parseInt(minutes * 60);
                    _<?php echo $b->bi_id ?>_seconds.innerHTML = pad(seconds);
                },1000);
            </script>
            <?php
            endif;
            endforeach;
            ?>
        </div>
        <div class="col-md-3"></div>
    </div>
</div>
<script type="text/javascript">
    $(".timer_s").click(function(){
        var id = $(this).data('id');
        var status = $(this).data('status');
        
        $.post('/save-break-info',{id : id, status : status},function(info){
            console.log(info);
            location.reload();
        },'json')
    });
    
    function pad(val){
        var valString = val + "";
        if(valString.length < 2){
            return "0" + valString;
        }
        else{
            return valString;
        }
    }
</script>
@endsection
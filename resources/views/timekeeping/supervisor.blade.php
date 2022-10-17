@extends('layouts.main')
@section('title')
View Profile
@endsection
@section('pagetitle')
Employee Break Information
@endsection
@section('content')
<div class="container-fluid">
    <style>
        .in_supth{
            background-color: #0275d8; color: white; width: 200px;
        }
        .in_timein{
         width: 150px;
        }
    </style>
    <form id="main_form">
        <div class="row">
            <div class="col-md-4">
                <select id="sel-staff" class="form-control" size="20" multiple>
                    <option value="0">SELECT ALL STAFF</option>
                    <?php
                        foreach($names as $ss):
                    ?>
                    <option value="<?php echo $ss->id ?>"<?php echo in_array($ss->id, $selected_staff) ? " selected" : "" ?>><?php echo $ss->last_name.", ".$ss->first_name ?></option>
                    <?php
                        endforeach;
                    ?>

                </select>
                <input type="hidden" id="sel-obj" name="selected_staff" value="">
            </div>
            <div class="col-md-8">
                <input type="submit" name="display" value="DISPLAY REPORT" class="btn btn-primary btn-lg">
                <input type="submit" name="download" value="DOWNLOAD EXCEL REPORT" class="btn btn-success btn-lg">
            </div>
        </div>
    </form>
    <table class="table table-bordered" style="visibility: hidden;">
        <tr>
            <th class="in_supth"></th>
            <td>Time In</td>
            <td>Breaks</td>
            <td>Time Out</td>
        </tr>
    </table>
<?php
/*
 * https://www.freecodecamp.org/news/5-ways-to-build-real-time-apps-with-javascript-5f4d8fe259f7/
 * Long Pooling Simple Realtime
 *      modified:   app/Http/Controllers/EmployeeInfoController.php
        modified:   app/Http/Controllers/TimeKeepingController.php
        modified:   app/Http/Controllers/UtilsController.php
        modified:   resources/views/layouts/menu/normal.blade.php
        modified:   resources/views/timekeeping/supervisor.blade.php
        modified:   routes/web.php
 *      modified:   app/Http/Controllers/TimeKeepingController.php
        modified:   resources/views/timekeeping/supervisor.blade.php
 *      modified:   resources/views/timekeeping/tk_main.blade.php
 */
if($process):
    foreach($staff as $s):
    ?>
        <table class="table table-bordered">
            <tr>
                <th class="in_supth"><?php echo $s['obj']->first_name." ".$s['obj']->last_name ?></th>
                <td class="in_timein">Time In:<br><b><?php echo $s['time_in'] ?></b></td>
                <td>
                <?php
                $count = count($s['breaks']);
                $i = 1;
                foreach($s['breaks'] as $break):
                    echo $break->break_type.": ";
                    echo $break->com_minutes ? "<b>".$break->com_minutes." hrs</b>" : "<b>On-going</b>";
                    if( $i == $count)
                       echo "";
                    else{
                         echo ", ";
                        $i++;
                    }
                endforeach;
                ?>
                </td>
                <td class="in_timein">Time Out:<br><b><?php echo $s['time_out'] ?></b></td>
            </tr>
        </table>
    <?php    
    endforeach;
endif;
?>
</div>
<script type="text/javascript">
    $("#main_form").submit(function(){
        $("#sel-obj").val($("#sel-staff").val());
    });
</script>
@endsection
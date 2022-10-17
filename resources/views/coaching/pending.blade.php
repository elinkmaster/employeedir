@extends('layouts.main')
@section('title')
For Acknowledgement
@endsection
@section('pagetitle')
For Acknowledgement
@endsection
@section('content')
<div class="container-fluid">
     <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12"><b style="color: #0000FF; font-size: 16px;">Pending Coaching Sessions</b></div>
            </div>
            <div class="row">
                <div class="col-md-1">&nbsp;</div>
                <div class="col-md-8">
                    <table class="table table-striped">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Linkee</th>
                            <th>Focus</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach($pending as $p):
                        ?>
                        <tr>
                            <td><?php echo date("F d, Y", strtotime($p->lnk_date)) ?></td>
                            <td><?php echo $p->link_type ?></td>
                            <td><?php echo $p->linkee ?></td>
                            <td><?php echo $p->focus ?></td>
                            <td><a href="/<?php echo $p->lt_link."/".$p->lnk_id ?>" class="btn btn-primary">View Coaching</a></td>
                        </tr>
                        <?php
                        endforeach;
                        ?>
                    </table>
                </div>
                <div class="col-md-3">&nbsp;</div>
            </div>
        </div>
    </div>    
</div>
<script type="text/javascript">
    $(function(){
        initVals();
    });
    
    function initVals(){
        console.log("Init Success!");
    }
</script>
@endsection
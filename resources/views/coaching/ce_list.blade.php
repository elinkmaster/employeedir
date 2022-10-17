@extends('layouts.main')
@section('title')
Linking Page
@endsection
@section('pagetitle')
Linking Page
@endsection
@section('content')
<div class="container-fluid">
    <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12"><b style="color: #0000FF; font-size: 16px;">Cementing Expectation List</b></div>
            </div>
            <div class="row">
                <div class="col-md-1">&nbsp;</div>
                <div class="col-md-7">
                    <table class="table table-bordered">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Focus</th>
                            <th>Linker</th>
                            <th>Linkee</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach($linking as $lk):
                        ?>
                        <tr>
                            <td><?php echo date("F d, Y", strtotime($lk->lnk_date)) ?></td>
                            <td><?php echo $lk->lnk_type_desc ?></td>
                            <td><?php echo $lk->focus_desc ?></td>
                            <td><?php echo $lk->lnk_linker_name ?></td>
                            <td><?php echo $lk->lnk_linkee_name ?></td>
                            <td><a class="btn btn-primary" href="/ce-expectation/<?php echo $lk->se_com_id ?>">VIEW</a></td>
                        </tr>
                        <?php    
                        endforeach;
                        ?>
                    </table>
                </div>
                <div class="col-md-4">&nbsp;</div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        initVals();
    });
    
    function initVals(){
        console.log("Init Success");
    }
</script>
@endsection
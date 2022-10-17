@extends('layouts.main')
@section('content')
<div class="container-fluid">
    <div class="panel panel-primary">
        @include('coaching.sub_menu')
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12"><b style="color: #0000FF; font-size: 16px;">My Personal Linking Sessions</b></div>
            </div>
            <div class="row">
                <div class="col-md-1">&nbsp;</div>
                <div class="col-md-7">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Date</th>
                            <th>Linker</th>
                            <th>Type</th>
                            <th>Focus</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach($my_links as $ll):
                        ?>
                        <tr>
                            <td><?php echo date("F d, Y",strtotime($ll->lnk_date)) ?></td>
                            <td><?php echo $ll->lnk_linker_name ?></td>
                            <td><?php echo $ll->link_type_desc ?></td>
                            <td><?php echo $ll->focus ?></td>
                            <td><?php echo $ll->link_button ?></td>
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
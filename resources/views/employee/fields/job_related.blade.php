<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label class="asterisk-required">Position</label>
            <input class="form-control" placeholder="Position" name="position_name" value="{{@$employee->position_name}}" required>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="asterisk-required">Account</label>
             <select class="select2 form-control" name="account_id" required>
                <option selected="" disabled="">Select</option>
                @foreach($accounts as $account)
                    <option <?php echo @$employee->account_id == $account->id ? "selected" : "" ; ?> value="{{$account->id}}">{{$account->account_name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Team/Department</label>
            <select class="select2 form-control" id="_team_name" name="team_name">
                <option selected="" disabled="">Select</option>
                @foreach($departments as $department)
                    <option data-_dept_code="<?php echo $department->department_code ?>" <?php echo $department->department_name == @$employee->team_name ? "selected" : "";?> value="{{ $department->department_name }}"> {{$department->department_name}}</option>
                @endforeach
            </select>
            <input id="_dept_code" type="hidden" name="dept_code" value="<?php echo $employee->dept_code ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Regularization Date</label>
            <input type="text" name="regularization_date" class="form-control datepicker" value="{{@$employee->regularization_date == '1970-01-01' ? '' : @$employee->regularization_date}}" autocomplete="off">
        </div>
    </div>
</div>
<div class="row">
<div class="col-md-3">
    <div class="form-group">
        <label>Manager</label>
       <select class="select2 form-control" name="manager_id">
            <option selected="" disabled="">Select</option>
           @foreach($supervisors as $supervisor)
                <option value="{{ $supervisor->id }}" <?php echo $supervisor->id == @$employee->manager_id ? "selected" : "" ; ?>> {{$supervisor->fullname()}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <label>Supervisor</label>
        <select class="select2 form-control"  name="supervisor_id" >
            <option selected="" disabled="">Select</option>
            @foreach($supervisors as $supervisor)
            <option value="{{ $supervisor->id }}" <?php echo $supervisor->id == @$employee->supervisor_id ? "selected" : "" ; ?>> {{$supervisor->fullname()}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <label>Hire Date</label>
        <input class="form-control datepicker" placeholder="Hire Date" name="hired_date" value="{{@$employee->datehired()}}">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <label>Production Date</label>
        <input class="form-control datepicker" placeholder="Hire Date" name="prod_date" value="{{@$employee->prodDate()}}">
    </div>
</div>
<!-- <div class="col-md-3">
    <div class="form-group">
        <label class="asterisk-required">Employee Status</label>
         <select class="select2 form-control" name="status_id" required>
            <option selected="" disabled="">Select</option>
            <option <?php echo @$employee->status == 1 ? "selected" : "" ; ?> value="1">Active</option>
            <option <?php echo @$employee->status == 2 ? "selected" : "" ; ?> value="2">Inactive</option>
        </select>
    </div>
</div> -->
<!-- Status 1 for active, (deleted_at != null || status = 2) -> inactive, to deactivate go to employee/edit -> deactivate buton-->
<input type="hidden" class="form-control" placeholder="Ext" name="status_id" value="{{@$employee->status || 1}}" >
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label >EXT</label>
            <input class="form-control" placeholder="Ext" name="ext" value="{{@$employee->ext}}" >
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label >Wave </label>
            <input class="form-control" placeholder="Wave" name="wave" value="{{@$employee->wave}}" >
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label >Resignation Date </label>
            <input class="form-control datepicker" placeholder="Resignation Date" name="resignation_date" value="{{@$details->resignation_date == '1970-01-01' ? '' : @$details->resignation_date}}" >
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>&nbsp;</label>
            <a id="btnViewMovments" class="btn btn-warning form-control text-dark" href="#" data-toggle="modal" data-target="#modalMovements">Movements/Transfer</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label>Rehirable</label>
            <select class="select2 form-control" name="rehirable" required>
                <option selected="" disabled="">Select</option>
                <option <?php echo @$details->rehirable == 1 ? "selected" : "" ; ?> value="1">Yes</option>
                <option <?php echo @$details->rehirable == 0 ? "selected" : "" ; ?> value="0">No</option>
            </select>
        </div>
    </div> 
    <div class="col-md-4">
        <div class="form-group">
            <label>Reason</label>
            <input type="text" name="rehire_reason" class="form-control" value="{{ @$details->rehire_reason }}">
        </div>
        
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <br><br>
        <input type="checkbox" name="all_access" <?php echo @$employee->all_access == 1 ? "checked" : "" ; ?>> &nbsp;
        <span for="all_access">can view information from other account ?</span>
        </div>
    </div>
</div>


</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Position</label>
            <input class="form-control" readonly="1" placeholder="Position" name="position_name" value="{{@$employee->position_name}}" required>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Account</label>
            <select disabled="1" class="select2 form-control" name="account_id" required>
                <option selected="">Select</option>
                @foreach($accounts as $account)
                    <option <?php echo @$employee->account_id == $account->id ? "selected" : "" ; ?> value="{{$account->id}}">{{$account->account_name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Team/Department</label>
            <select class="select2 form-control" disabled="1" name="team_name">
                <option selected="">Select</option>
                @foreach($departments as $department)
                    <option <?php echo $department->department_name == @$employee->team_name ? "selected" : "";?> value="{{ $department->department_name }}"> {{$department->department_name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group reg_div_">
            <label>Regularization Date</label>
            <input type="text" readonly="1" name="regularization_date" class="form-control datepicker" value="{{@$employee->regularization_date}}" autocomplete="off">
        </div>
    </div>
</div>
<div class="row">
<div class="col-md-3">
    <div class="form-group">
        <label>Manager</label>
        <select disabled="1" class="select2 form-control" name="manager_id">
            <option>Select</option>
           @foreach($supervisors as $supervisor)
                <option value="{{ $supervisor->id }}" <?php echo $supervisor->id == @$employee->manager_id ? "selected" : "" ; ?>> {{$supervisor->fullname()}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <label>Supervisor</label>
        <select class="select2 form-control" disabled="1"  name="supervisor_id" >
            <option>Select</option>
            @foreach($supervisors as $supervisor)
            <option value="{{ $supervisor->id }}" <?php echo $supervisor->id == @$employee->supervisor_id ? "selected" : "" ; ?>> {{$supervisor->fullname()}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <label>Hire Date</label>
        <input class="form-control" readonly="1" placeholder="Hire Date" name="hired_date" value="{{@$employee->datehired()}}">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <label>Production Date</label>
        <input class="form-control" readonly="1" placeholder="Hire Date" name="prod_date" value="{{@$employee->prodDate()}}">
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
<div class="col-md-2">
    <div class="form-group">
        <label >EXT</label>
        <input readonly="1" class="form-control" placeholder="Ext" name="ext" value="{{@$employee->ext}}" >
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label >Resignation Date </label>
        <input class="form-control" readonly="1" placeholder="Resignation Date" name="resignation_date" value="{{@$details->resignation_date == '1970-01-01' ? '' : @$details->resignation_date}}" >
    </div>
</div>

    <div class="col-md-2">
        <div class="form-group">
            <label>Rehirable</label>
            <select class="select2 form-control" name="rehirable" required disabled="">
                <option selected="" disabled="">Select</option>
                <option <?php echo @$details->rehirable == 1 ? "selected" : "" ; ?> value="1">Yes</option>
                <option <?php echo @$details->rehirable == 0 ? "selected" : "" ; ?> value="0">No</option>
            </select>
        </div>
    </div> 
    <div class="col-md-4">
        <div class="form-group">
            <label>Reason</label>
            <input type="text" name="rehire_reason" class="form-control" value="{{ @$details->rehire_reason }}" readonly="">
        </div>
        
    </div>

<div class="col-md-12">
    <div class="form-group">
        <input type="checkbox" disabled="1" name="all_access" <?php echo @$employee->all_access == 1 ? "checked" : "" ; ?>> &nbsp;
        <span for="all_access">can view information from other account ?</span>
    </div>
</div>
</div>
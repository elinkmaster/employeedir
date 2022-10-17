<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>First Name</label>
            <input  class="form-control" placeholder="First Name" name="first_name" value="{{@$employee->first_name}}" readonly="1"> 
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Middle Name</label>
            <input class="form-control" placeholder="Middle Name" name="middle_name" value="{{@$employee->middle_name}}" readonly="1">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Last Name</label>
            <input class="form-control" placeholder="Last Name" name="last_name" value="{{@$employee->last_name}}" readonly="1">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Employee ID</label>
            <input class="form-control" placeholder="Employee ID" name="eid" value="{{@$employee->eid}}" maxLength="20" readonly="1">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label >Phone Name</label>
            <input class="form-control" placeholder="Phone Name" name="alias" value="{{@$employee->alias}}" readonly="1">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Birthdate</label>
            <input class="form-control" placeholder="Birthdate" name="birth_date" value="{{ @$employee->birthdate() }}" readonly="1">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Contact Number</label>            
            <input type="text" class="form-control" name="contact_number" maxLength="20" value="{{@$employee->contact_number}}" readonly="1">
        </div>
    </div>
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-3 form-group">
                <label>Gender</label>
                <br>
                <input type="radio" id="male" name="gender_id" value="1" disabled="1" placeholder="test" <?php echo @$employee->gender == 1 ? "checked" : "" ; ?>>
                <label class="radio-label" for="male">Male</label>
                &nbsp;
                &nbsp;
                <input type="radio" id="female" name="gender_id" value="2" disabled="1" placeholder="test" <?php echo @$employee->gender == 2 ? "checked" : "" ; ?>>
                <label class="radio-label" for="female" >Female</label>
            </div>
            <div class="col-md-3 form-group">
                <label>Civil Status</label>
                <br>
                <select name="civil_status" class="select2" disabled="1">
                    <option value="1"{{ @$employee->civil_status == 1 ? 'selected' : '' }}>Single</option>
                    <option value="2"{{ @$employee->civil_status == 2 ? 'selected' : '' }}>Married</option>
                    <option value="3"{{ @$employee->civil_status == 3 ? 'selected' : '' }}>Separated</option>
                    <option value="4"{{ @$employee->civil_status == 4 ? 'selected' : '' }}>Annulled</option>
                    <option value="5"{{ @$employee->civil_status == 5 ? 'selected' : '' }}>Divorced</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                    <label>Avega Number</label>
                <br>
                <input type="text" class="form-control" name="avega_num" value="{{ isset($details->avega_num) ? $details->avega_num : "" }}" readonly="1">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <br>
        <label>Father's Name</label>
        <br>
        <input class="form-control" name="fathers_name" value="<?php echo isset($details->fathers_name) ? $details->fathers_name : "" ?>" readonly="1">
    </div>
    <div class="col-md-4 form-group">
        <br>
        <label>Complete Mother's Maiden Name</label>
        <br>
        <input class="form-control" name="mothers_name" value="<?php echo isset($details->mothers_name) ? $details->mothers_name : "" ?>" readonly="1">
    </div>
    <div class="col-md-4 form-group">
        <br>
        <label>Spouse's Name</label>
        <br>
        <input class="form-control" name="spouse_name" value="<?php echo isset($details->spouse_name) ? $details->spouse_name : "" ?>" readonly="1">
    </div>
</div>

<div class="row">
    <div class="col-md-4 form-group">
        <label>Father's Birthday</label>
        <br>
        <input class="form-control" readonly="1" name="fathers_bday" value="<?php echo isset($details->fathers_bday) ? date("m/d/Y", strtotime($details->fathers_bday )) : "" ?>" autocomplete="off">
    </div>
    <div class="col-md-4 form-group">
        <label>Mother's Birthday</label>
        <br>
        <input class="form-control" readonly="1" name="mothers_bday" value="<?php echo isset($details->mothers_bday) ? date("m/d/Y", strtotime($details->mothers_bday )) : "" ?>" autocomplete="off">
    </div>
    <div class="col-md-4 form-group">
        <label>Spouse's Birthday</label>
        <br>
        <input class="form-control" readonly="1" name="spouse_bday" value="<?php echo isset($details->spouse_bday ) ? date("m/d/Y", strtotime($details->spouse_bday )) : "" ?>" autocomplete="off">
    </div>
</div>

<div id="dependentsDiv" class="col-md-12" style="border-style: solid; border-width: thin; border-color: blue; margin-bottom: 20px;">
    <div class="row">
        <div class="col-md-4 form-group">
            <br>
            <label>Dependent's Name</label>
            <br>
            <input class="form-control" readonly="1" name="dependent_name[]" value="<?php echo count($dependents) > 0 ? $dependents[0]->dependent : "" ?>">
        </div>
        <div class="col-md-4 form-group">
            <br>
            <label>Birthday</label>
            <br>
            <input class="form-control" readonly="1" name="dependent_bday[]" value="<?php echo count($dependents) > 0 ? date("m/d/Y",strtotime($dependents[0]->bday)) : "" ?>" autocomplete="off">
        </div>
        <div class="col-md-3 form-group">
            <br>
            <label>Generali Number</label>
            <br>
            <input class="form-control" name="generali_num[]" value="<?php echo count($dependents) > 0 ? $dependents[0]->generali_num : "" ?>" autocomplete="off" readonly="1">
        </div>
    </div>                                
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>City Address</label>
            <textarea name="address" class="form-control" readonly="1" maxLength="200" rows="4" style="width: 75%; border-radius: 0">{{ @$employee->address }}</textarea>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Home Town Address</label>
            <textarea name="town_address" class="form-control" readonly="1" rows="4">{{ @$details->town_address }}</textarea>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>In case of emergency please contact.</label>
            <br>
            <input type="text" name="em_con_name" readonly="1" class="form-control" value="{{ @$details->em_con_name }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Relationship</label>
            <br>
            <input type="text" name="em_con_rel" readonly="1" class="form-control" value="{{ @$details->em_con_rel }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Contact Number</label>
            <br>
            <input type="text" name="em_con_num" readonly="1" class="form-control" value="{{ @$details->em_con_num }}">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Address</label>
            <textarea name="em_con_address" readonly="1" class="form-control" rows="4">{{ @$details->em_con_address }}</textarea>
        </div>
    </div>
    <div class="col-md-6">&nbsp;</div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Company Email</label>
            <input class="form-control" readonly="1" placeholder="Email" name="email" value="{{$employee->email}}">
        </div>
    </div>
         <div class="col-md-4">
            <div class="form-group">
                <label>Personal Email</label>
                <input class="form-control" readonly="1" placeholder="Email 2" name="email2" type="email" value="{{$employee->email2}}">
            </div>
        </div>
    <!--
         <div class="col-md-4">
            <div class="form-group">
                <label>Email 3</label>
                <input class="form-control" placeholder="Email 3" name="email3" type="email" value="{{$employee->email3}}">
            </div>
        </div>
    -->
    <div class="col-md-4 hidden password">
        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" placeholder="Password" name="password" >
        </div>
    </div>
    <div class="col-md-4 hidden password">
        <div class="form-group">
            <label>Confirm Password</label>
            <input class="form-control" placeholder="Confirm Password">
        </div>
    </div>
    <!-- <a type="button" class="btn btn-default" style="margin-top: 26px;" href="{{url('employee/'. $employee->id .'/changepassword')}}">Change Password</a> -->
</div>
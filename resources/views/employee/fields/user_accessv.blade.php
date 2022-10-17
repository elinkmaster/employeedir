<div class="col-md-12">
  <div class="form-group">
      <input type="radio" disabled="1" {{ $employee->usertype == 1 ? 'checked' : ''}} id="employee" name="employee_type" value="1" placeholder="test">
      <label class="radio-label" for="employee">Employee</label>
      &nbsp;
      &nbsp;
      <input type="radio" disabled="1" {{ $employee->usertype == 2 ? 'checked' : ''}} id="supervisor" name="employee_type" value="2" placeholder="test">
      <label class="radio-label" for="supervisor">Supervisor</label>
      &nbsp;
      &nbsp;
      <input type="radio" disabled="1" {{ $employee->usertype == 3 ? 'checked' : ''}} id="manager" name="employee_type" value="3" placeholder="test">
      <label class="radio-label" for="manager">Manager</label>
      &nbsp;
      &nbsp;
      &nbsp;
      &nbsp;
      |
      &nbsp;
      &nbsp;
      &nbsp;
      &nbsp;
      <input type="checkbox" disabled="1" {{ $employee->is_admin == 1 ? 'checked' : ''}} id="admin" name="is_admin">

      <label class="radio-label" for="admin">WebsiteAdmin</label>
      &nbsp;
      &nbsp;
      &nbsp;
      &nbsp;
      <input type="checkbox" disabled="1" {{ $employee->is_hr == 1 ? 'checked' : ''}} id="hr" name="is_hr">

      <label class="radio-label" for="hr">HR</label>
      &nbsp;
      &nbsp;
      &nbsp;
      &nbsp;
      <input type="checkbox" disabled="1" {{ $employee->is_erp == 1 ? 'checked' : ''}} id="erp" name="is_erp">

      <label class="radio-label" for="erp">ERP</label>
      &nbsp;
      &nbsp;
      <select name="is_regular" disabled="1" class="select2 is_reg_event">
          <option value="-1">Employee Type</option>
          <option value="0" {{ $employee->is_regular == 0 ? 'selected' : ''}}>Probationary</option>
          <option value="1" {{ $employee->is_regular == 1 ? 'selected' : ''}}>Regular</option>
          <option value="2" {{ $employee->is_regular == 2 ? 'selected' : ''}}>Project Based</option>
      </select>
      &nbsp;
      &nbsp;
      <select name="employee_category" disabled="1" class="select2">
          <option value="0">Employee Category</option>
          <option value="1" {{ $employee->employee_category == 1 ? 'selected' : ''}}>Manager</option>
          <option value="2" {{ $employee->employee_category == 2 ? 'selected' : ''}}>Supervisor</option>
          <option value="3" {{ $employee->employee_category == 3 ? 'selected' : ''}}>Support</option>
          <option value="4" {{ $employee->employee_category == 4 ? 'selected' : ''}}>Rank</option>
      </select>
  </div>
<div class="my-2">
        <small>Linkees</small>
        <div class="my-2 d-flex gap-2  p-2" style="width: 100%;flex-wrap: wrap;" id="linkees">
            @foreach ($linkees as $linkee)
            <div class="border border-success rounded-pill p-2" id="linkee-{{$linkee->id}}"
                style="font-size: 12px; min-width:100px;">
                <input type="hidden" name="linkee-{{$linkee->id}}" value="{{$linkee->id}}">
                <span>{{$linkee->last_name}}, {{$linkee->first_name}}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{asset('public/css/custom-bootstrap.css')}}">


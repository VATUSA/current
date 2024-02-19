<select class="form-control" id="facilityEmail">
    <option value=0>Select an address</option>
    @foreach(['atm','datm','ta','ec','fe','wm'] as $role)
        <option value="{{$role}}">{{strtolower($fac)}}-{{$role}}@vatusa.net</option>
    @endforeach
</select>
<div id="emailBox" style="display: none;">
    <div class="form-group">
        <label class="control-label">Destination (separate multiple addresses with a
            comma)</label>
        <input class="form-control" type="text" id="emailDestination"
               placeholder="Destination email address">
    </div>
    <div class="form-group">
        <label class="control-label">Static?</label>
        <select class="form-control" id="emailStatic">
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>
    <button class="btnEmailSave btn btn-primary">Save</button>
</div>
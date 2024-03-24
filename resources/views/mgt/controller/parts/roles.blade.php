@php
    $isVATUSAStaff = \App\Helpers\AuthHelper::authACL()->isVATUSAStaff();

@endphp

<div class="row">
    <div class="col-md-6">
        <table class="table table-striped panel panel-default">
            <thead>
            <tr style="background:#F5F5F5" class="panel-heading">
                <td colspan="4" style="text-align:center"><h3 class="panel-title">Assigned Roles</h3>
                </td>
            </tr>
            </thead>
            @foreach ($assignedRoles as $role)
                <tr style="text-align: center">
                    <td>{{ $role->facility }}</td>
                    <td>{{ $role->title->title }}</td>
                    <td>
                        @if(\App\Helpers\RoleHelperV2::canAssignRole($user->cid, $role->role, $role->facility))
                            <form action="/mgt/controller/{{$user->cid}}/role/revoke" method="POST">
                                <input type="hidden" name="facility" value="{{$role->facility}}"/>
                                <input type="hidden" name="role" value="{{$role->role}}"/>
                                <button class="btn btn-danger" type="submit">
                                    <i class="fa fa-times"></i> Revoke
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="col-md-6">
        @if($user->flag_preventStaffAssign)
            <div class="text-center">
                <h4>This user is not eligible for staff roles.</h4>
                <p>Please check the action log and contact VATUSA2 or VATUSA3 (as appropriate) for details.</p>
            </div>
        @else
            @if($isVATUSAStaff)
                <h4>Global Roles</h4>
                <form action="/mgt/controller/{{$user->cid}}/role/assign" method="POST">
                    <input type="hidden" name="facility" value="ZHQ"/>
                    <label for="grRole">Role</label>
                    <select id="grRole" name="role">
                        <option>---</option>
                        @foreach (\App\Helpers\RoleHelperV2::roleTitles(\App\Helpers\RoleHelperV2::$globalRoles) as $role)
                            <option value="{{$role->role}}">{{$role->title}}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-success" type="submit">
                        <i class="fa fa-plus"></i> Assign
                    </button>
                </form>
            @endif
            <h4>Facility Staff Roles</h4>
            <form action="/mgt/controller/{{$user->cid}}/role/assign" method="POST">
                <label for="frFacility">Facility</label>
                <select id="frFacility" name="facility">
                    <option>---</option>
                    @foreach(\App\Models\Facility::where('active', 1)->orderby('id', 'ASC')->get() as $f)
                        @if($isVATUSAStaff || \App\Classes\RoleHelper::isFacilitySeniorStaff(null, $f->id, false, false))
                            <option value="{{$f->id}}">{{$f->id}}</option>
                        @endif
                    @endforeach
                </select>
                <label for="frRole">Role</label>
                <select id="frRole" name="role">
                    <option>---</option>
                    @if($isVATUSAStaff)
                        @foreach (\App\Helpers\RoleHelperV2::roleTitles(\App\Helpers\RoleHelperV2::$facilityRolesUSA) as $role)
                            <option value="{{$role->role}}">{{$role->title}}</option>
                        @endforeach
                    @endif
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA())
                        @foreach (\App\Helpers\RoleHelperV2::roleTitles(\App\Helpers\RoleHelperV2::$facilityRolesATM) as $role)
                            <option value="{{$role->role}}">{{$role->title}}</option>
                        @endforeach
                    @endif
                    @foreach (\App\Helpers\RoleHelperV2::roleTitles(\App\Helpers\RoleHelperV2::$facilityRolesTA) as $role)
                        <option value="{{$role->role}}">{{$role->title}}</option>
                    @endforeach
                </select>
                <button class="btn btn-success" type="submit">
                    <i class="fa fa-plus"></i> Assign
                </button>
            </form>
        @endif

    </div>
</div>

@if($isVATUSAStaff)
    <div class="form-group">
        <label class="col-sm-2 control-label">Prevent Staff Role Assignment</label>
        <div class="col-sm-10">
                                    <span id="toggleStaffPrevent" style="font-size:1.8em;">
                                        <i class="toggle-icon fa fa-toggle-{{ $user->flag_preventStaffAssign ? "on text-danger" : "off text-info"}} "></i>
                                        <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                                    </span>
            <p class="help-block">This will prevent the controller from being assigned a
                staff role by facility staff. <br> Only a VATUSA Staff Member will be
                able
                to
                assign him or her a role.</p>
        </div>
    </div>
@endif
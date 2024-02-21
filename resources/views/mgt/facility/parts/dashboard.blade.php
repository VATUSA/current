<table class="fac-dash">
    <tr>
        <td style="width: 33%"><h1><span data-toggle="tooltip" data-placement="bottom"
                                         title="Total Controllers"><i
                            class="fa fa-user"></i> {{\App\Models\User::where('facility',$fac)->count()}}</span>
            </h1></td>
        <td style="width: 34%"><h1><span data-toggle="tooltip" data-placement="bottom"
                                         title="Pending Transfers"><i
                            class="fa fa-user-plus"></i> {{\App\Models\Transfers::where('to', $fac)->where('status', 0)->count()}}</span>
            </h1></td>
        <td><h1><span data-toggle="tooltip" data-placement="bottom"
                      title="Total Eligible for Promotion">
                                                  <i class="fas fa-school"></i> {{$promotionEligible}}</span></h1></td>
    </tr>
</table>
@if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
    <hr>
    <h4>
        Facility Staff Point of Contact
        <a href="/mgt/roles/{{$fac}}">
            <button type="submit" class="btn btn-info">
                <i class="fa fa-wrench"></i> View All Facility Roles
            </button>
        </a>
    </h4>
    <form action="/mgt/facility/{{$fac}}/staffPOC" method="POST">
        @endif
        <div id="staff-table">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Position</th>
                    <th>Name</th>
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                        <th>Select</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Air Traffic Manager (ATM)</td>
                    <td>{{$atm}}</td>
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                        <td>
                            @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                <select name="atm">
                                    <option value="-1">None</option>
                                    @foreach($staffPOCOptions["ATM"] as $cid => $name)
                                        <option value="{{ $cid }}" @if($cid == $facility->atm) selected @endif>
                                            {{ $name }} ({{ $cid }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </td>
                    @endif
                </tr>
                <tr>
                    <td>Deputy Air Traffic Manager (DATM)</td>
                    <td>{{$datm}}</td>
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                        <td>
                            @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                <select name="datm">
                                    <option value="-1">None</option>
                                    @foreach($staffPOCOptions["DATM"] as $cid => $name)
                                        <option value="{{ $cid }}" @if($cid == $facility->datm) selected @endif>
                                            {{ $name }} ({{ $cid }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </td>
                    @endif
                </tr>
                <tr>
                    <td>Training Administrator (TA)</td>
                    <td>{{$ta}}</td>
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                        <td>
                            @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                <select name="ta">
                                    <option value="-1">None</option>
                                    @foreach($staffPOCOptions["TA"] as $cid => $name)
                                        <option value="{{ $cid }}" @if($cid == $facility->ta) selected @endif>
                                            {{ $name }} ({{ $cid }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </td>
                    @endif
                </tr>
                <tr>
                    <td>Events Coordinator (EC)</td>
                    <td>{{$ec}}</td>
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                        <td>
                            <select name="ec">
                                <option value="-1">None</option>
                                @foreach($staffPOCOptions["EC"] as $cid => $name)
                                    <option value="{{ $cid }}" @if($cid == $facility->ec) selected @endif>
                                        {{ $name }} ({{ $cid }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    @endif
                </tr>
                <tr>
                    <td>Facility Engineer (FE)</td>
                    <td>{{$fe}}</td>
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                        <td>
                            <select name="fe">
                                <option value="-1">None</option>
                                @foreach($staffPOCOptions["FE"] as $cid => $name)
                                    <option value="{{ $cid }}" @if($cid == $facility->fe) selected @endif>
                                        {{ $name }} ({{ $cid }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    @endif
                </tr>
                <tr>
                    <td>Webmaster (WM)</td>
                    <td>{{$wm}}</td>
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                        <td>
                            <select name="wm">
                                <option value="-1">None</option>
                                @foreach($staffPOCOptions["WM"] as $cid => $name)
                                    <option value="{{ $cid }}" @if($cid == $facility->wm) selected @endif>
                                        {{ $name }} ({{ $cid }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    @endif
                </tr>
                </tbody>
            </table>
        </div>
        @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
            <div class="text-center">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Save Facility Staff POCs
                </button>
            </div>
    </form>
@endif
<table class="table table-hover">
    <thead>
    <tr>
        <th>Position</th>
        <th>Name</th>
        <th>Options</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Air Traffic Manager (ATM)</td>
        <td>{{$atm}}</td>
        @if (\App\Classes\RoleHelper::isVATUSAStaff())
        <td><a href="#" onClick="posEdit(1)"><i class="fa fa-edit"></i></a> &nbsp; <a href="#" onclick="posDel(1)"><i class="text-danger fa fa-times"></i></a></td>
        @else
        <td>&nbsp;</td>
        @endif
    </tr>
    <tr>
        <td>Deputy Air Traffic Manager (DATM)</td>
        <td>{{$datm}}</td>
        @if (\App\Classes\RoleHelper::isVATUSAStaff())
            <td><a href="#" onClick="posEdit(2)"><i class="fa fa-edit"></i></a> &nbsp; <a href="#" onclick="posDel(2)"><i class="text-danger fa fa-times"></i></a></td>
        @else
            <td>&nbsp;</td>
        @endif
    </tr>
    <tr>
        <td>Training Administrator (TA)</td>
        <td>{{$ta}}</td>
        @if (\App\Classes\RoleHelper::isVATUSAStaff())
            <td><a href="#" onClick="posEdit(3)"><i class="fa fa-edit"></i></a> &nbsp; <a href="#" onclick="posDel(3)"><i class="text-danger fa fa-times"></i></a></td>
        @else
            <td>&nbsp;</td>
        @endif
    </tr>
    <tr>
        <td>Events Coordinator (EC)</td>
        <td>{{$ec}}</td>
        @if (\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
            <td><a href="#" onClick="posEdit(4)"><i class="fa fa-edit"></i></a> &nbsp; <a href="#" onclick="posDel(4)"><i class="text-danger fa fa-times"></i></a></td>
        @else
            <td>&nbsp;</td>
        @endif
    </tr>
    <tr>
        <td>Facility Engineer (FE)</td>
        <td>{{$fe}}</td>
        @if (\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
            <td><a href="#" onClick="posEdit(5)"><i class="fa fa-edit"></i></a> &nbsp; <a href="#" onclick="posDel(5)"><i class="text-danger fa fa-times"></i></a></td>
        @else
            <td>&nbsp;</td>
        @endif
    </tr>
    <tr>
        <td>Webmaster (WM)</td>
        <td>{{$wm}}</td>
        @if (\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
            <td><a href="#" onClick="posEdit(6)"><i class="fa fa-edit"></i></a> &nbsp; <a href="#" onclick="posDel(6)"><i class="text-danger fa fa-times"></i></a></td>
        @else
            <td>&nbsp;</td>
        @endif
    </tr>
    </tbody>
</table>
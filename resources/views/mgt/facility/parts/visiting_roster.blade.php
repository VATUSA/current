<br>
@if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM"))
    <div class="text-center">
        <button class="btn btn-success" data-toggle="modal" data-target="#addVisitorModal">
            <i
                    class="fa fa-plus"></i> Add Visitor
        </button>
    </div>
@endif
<br>
<div id="vrosterloading">
    <center><img src="/img/gears.gif"><br><br>Loading visiting roster...</center>
</div>
<table class="table table-hover table-condensed tablesorter" id="vrostertable"
       style="display: none;">
    <thead>
    <tr>
        <th>CID</th>
        <th>Name</th>
        <th>Rating</th>
        <th>Home Facility</th>
        <th>Date Added</th>
        <td class="text-right">Options</td>
    </tr>
    </thead>
    <tbody id="vrostertablebody">
    </tbody>
</table>
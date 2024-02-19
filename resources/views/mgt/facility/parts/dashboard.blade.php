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
<hr>
<h4>Facility Staff Administration</h4>
<div id="staff-table"></div>
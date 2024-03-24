<table class="table table-hover">
    <thead>
    <tr>
        <th>Date</th>
        <th>CID</th>
        <th>Name</th>
        <th>Rating</th>
        <th>Options</th>
    </tr>
    </thead>
    <tbody>
    <?php $x = 0; ?>
    @foreach(\App\Models\Transfers::where('to', $fac)->where('status', 0)->get() as $t)
            <?php
            $user = App\Models\User::where('cid', $t->cid)->first();
            $x = 1;
            ?>
        <tr id="trans{{$t->id}}">
            <td>{{$t->created_at}}</td>
            <td>{{$user->cid}}</td>
            <td>{{$user->fname}} {{$user->lname}}</td>
            <td>{{$user->urating->short}}</td>
            @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityRoster($fac))
                <td>
                    <a href="/mgt/controller/{{$t->cid}}" target="_blank">
                        <i class="fa fa-search"></i>
                    </a>
                    &nbsp;
                    <a href="#" onClick="appvTrans({{$t->id}})">
                        <i class="fa fa-check"></i>
                    </a>
                    &nbsp;
                    <a href="#" onClick="rejTrans({{$t->id}})">
                        <i class="fa fa-times"></i>
                    </a>
                </td>
            @else
                <td>&nbsp;</td>
            @endif
        </tr>
    @endforeach
    @if ($x == 0)
        <tr>
            <td colspan="5">
                <center>No pending transfers.</center>
            </td>
        </tr>
    @endif
    </tbody>
</table>
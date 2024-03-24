<div class="row">
    <div class="col-md-6">
        <table class="table table-striped panel panel-default">
            <thead>
            <tr style="background:#F5F5F5" class="panel-heading">
                <td colspan="4" style="text-align:center"><h3 class="panel-title">Rating
                        History</h3>
                </td>
            </tr>
            </thead>
            @foreach (\App\Models\Promotions::where('cid', $user->cid)->orderby('created_at', 'desc')->get() as $promo)
                <tr style="text-align: center">
                    <td style="width:30%">{!! $promo->created_at->format('m/d/Y') !!}
                        <br><em>{{ \App\Classes\Helper::nameFromCID($promo->grantor) }}</em>
                    </td>
                    <td style="vertical-align: middle">
                        <strong>{{ \App\Classes\Helper::ratingShortFromInt($promo->from) }}</strong>
                    </td>
                    <td style="vertical-align: middle"
                        class="{{(($promo->from < $promo->to)? 'text-success' : 'text-danger')}}">
                        <i
                                class="fa {{(($promo->from < $promo->to) ? 'fa-arrow-up' : 'fa-arrow-down')}}"></i>
                    </td>
                    <td style="vertical-align: middle">
                        <strong>{{ \App\Classes\Helper::ratingShortFromInt($promo->to) }}</strong>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    @if(\App\Helpers\AuthHelper::authACL()->isFacilitySeniorStaff() ||
        \App\Helpers\AuthHelper::authACL()->isWebmaster($user->facility))
        <div class="col-md-6">
            <table class="table table-striped panel panel-default">
                <thead>
                <tr style="background:#F5F5F5" class="panel-heading">
                    <td colspan="5" style="text-align:center"><h3 class="panel-title">
                            Transfer
                            History</h3>
                    </td>
                </tr>
                </thead>
                @foreach(\App\Models\Transfers::where('cid', $user->cid)->orderby('id', 'desc')->get() as $t)
                    <tr style="text-align: center">
                        <td>{{substr($t->updated_at, 0,10)}}</td>
                        <td><strong>{{$t->from}}</strong></td>
                        <td class="text-{{($t->status == 2 ? 'danger' : ($t->status == 1 ? 'success' : 'warning'))}}">
                            <i class="fa fa-arrow-right" data-toggle="tooltip"
                               data-original-title="{{($t->status == 2 ? 'Declined - '.$t->actiontext.' by '.\App\Classes\Helper::nameFromCID($t->actionby, 1) : ($t->status == 1 ? 'Approved by '.\App\Classes\Helper::nameFromCID($t->actionby, 1) : 'Pending'))}}"
                               style="cursor: pointer"></i></td>
                        <td><strong>{{$t->to}}</strong></td>
                        <td><a href="#" onClick="viewXfer({{$t->id}})"><i
                                        class="fa fa-search"></i></a>
                        </td>
                    </tr>
                @endforeach
                @if(\App\Helpers\AuthHelper::authACL()->isVATUSAStaff() && $user->flag_homecontroller == 1)
                    <tr>
                        <td colspan="5">Transfer Waiver: <span id="waiverToggle"><i
                                        id="waivertogglei"
                                        class="fa {{(($user->flag_xferOverride==1) ? "fa-toggle-on text-success" : "fa-toggle-off text-danger")}}"></i></span>
                            <a href="/mgt/transfer?cid={{$user->cid}}">Submit TR</a>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    @endif
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Action Log
        </h3>
    </div>
    <div class="panel-body">
        @if(\App\Helpers\AuthHelper::authACL()->isVATUSAStaff())
            <div class="alert alert-success" id="delete-log-success" style="display:none;">
                <strong><i class='fa fa-check'></i> Success! </strong> The log entry has
                been
                deleted.
            </div>
            <div class="alert alert-danger" id="delete-log-error" style="display:none;">
                <strong><i
                            class='fa fa-check'></i> Error! </strong> Could not delete log
                entry.
            </div>
        @endif
        @if(\App\Helpers\AuthHelper::authACL()->isFacilitySeniorStaff())
            <form class="form-horizontal"
                  action="{{url("/mgt/controller/action/add")}}"
                  method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="to" value="{{ $user->cid }}">
                <input type="hidden" name="from" value="{{ Auth::user()->cid }}">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Add a Log Entry</label>
                    <div class="col-sm-10"><textarea class="form-control" rows="2"
                                                     name="log"
                                                     placeholder="Entry"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success sub-action-btn"
                                value="submit">
                            <i class="fa fa-check"></i> Submit
                        </button>
                    </div>
                </div>
            </form>
            <hr>
        @endif
        <table class="table table-striped">
            @foreach(\App\Models\Actions::where('to', $user->cid)->orderby('id', 'desc')->get() as $a)
                <tr id="log-{{ $a->id }}">
                    <td style="width:10%"><strong>{{substr($a->created_at, 0,10)}}</strong>
                    </td>
                    <td class="log-content">{{$a->log}}
                        @php $name = \App\Classes\Helper::nameFromCID($a->from) @endphp
                        @if($a->from && !str_contains($a->log, "by $name"))
                            <p class="help-block">Added
                                by {{ $name }}</p>
                        @endif</td>
                    <td>
                        @if(App\Helpers\AuthHelper::authACL()->isVATUSAStaff() && $a->from &&
                        !str_contains($a->log, 'by ' . App\Classes\Helper::nameFromCID($a->from)))
                            <a data-id="{{ $a->id }}"
                               href="#"
                               data-action="{{ url('mgt/controller/action/delete/'.$a->id) }}"
                               class="text-danger delete-log"><i
                                        class="fa fa-times"></i></a>
                            <i class="spinner-icon fa fa-spinner fa-spin"
                               style="display:none;"></i>

                        @else &nbsp;
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
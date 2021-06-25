@extends('layout')
@section('title', 'CBT')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">

                    <select id="fac" class="mgt-sel">
                        @foreach(\App\Models\Facility::where('active', 1)->orWhere('id','ZAE')->orderBy('name')->get() as $f)
                            <option name="{{$f->id}}" @if($f->id == $fac) selected="true" @endif>{{$f->id}}</option>
                        @endforeach
                    </select>&nbsp;-&nbsp;

                    {{$facname}} CBTs
                </h3>
            </div>
            <div class="panel-body">
                <table width="100%" class="table">
                    <thead>
                    <tr>
                        <th>Chapter</th>
                        <th>Name</th>
                        <th>Completed?</th>
                    </tr>
                    </thead>
                    <tbody style="font-weight: 600">
                    @foreach($blocks as $block)
                        @if (($block->level == "Senior Staff" && \App\Classes\RoleHelper::isFacilitySeniorStaff()) ||
                             ($block->level == "Staff" && \App\Classes\RoleHelper::isFacilityStaff()) ||
                             ($block->level == "I1" && \App\Classes\RoleHelper::isInstructor()) ||
                             ($block->level == "C1" && (\Auth::check() && \Auth::user()->rating >= \App\Classes\Helper::ratingIntFromShort("C1"))) ||
                             ($block->level == "S1" && (\Auth::check() && \Auth::user()->rating >= \App\Classes\Helper::ratingIntFromShort("S1"))) ||
                              $block->level == "ALL")
                            <tr>
                                <td colspan="3">
                                    <center><strong>Block {{$block->order}}: {{$block->name}}</strong></center>
                                </td>
                            </tr>
                            @foreach($block->chapters as $chapter)
                                @if($chapter->url != "")
                                    <tr onClick="openCBT('{{$chapter->url}}','{{$chapter->id}}')">
                                        <td width="10%">{{$chapter->order}}</td>
                                        <td width="80%">{{$chapter->name}}</td>
                                        <td width="10%" style="text-align: center;"
                                            id="compl{{$chapter->id}}">@if (\App\Classes\CBTHelper::isComplete($chapter->id))
                                                <i class="fa fa-check text-success"></i> @endif</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cbtmodal" tabindex="-1" role="dialog" aria-labelledby="cbtmodal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width: 1050px; margin-left: -230px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalLabel">CBT Viewer</h4>
                </div>
                <div class="modal-body" id="cbtbody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-md" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#cbtmodal').on('hidden.bs.modal', function () {
                $('#cbtbody').html("");
            });
            $('#fac').change(function () {
                window.location = "/cbt/" + $('#fac').val();
            });
        });

        function openCBT(url, chid) {
            $('#cbtbody').html("");
            if (url.indexOf('http') == -1)
                $('#cbtbody').html('<center><iframe id="iframe" src="https://docs.google.com/presentation/d/' + url + '/embed?start=false&loop=false&delayms=60000" style="width: 1000px; height: 593px;" frameborder="0" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe></center>');
            else
                $('#cbtbody').html('<center><iframe id="iframe" src="' + url + '" style="width: 100%; height: 593px;" frameborder="0" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe></center>');

            $('#cbtmodal').modal('show');

            $.ajax({
                type: "PUT",
                url: "/cbt/" + chid,
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                },
                success: function () {
                    $('#compl' + chid).html('<i class="fa fa-check text-success"></i>');
                }
            });
        }
    </script>
@endsection
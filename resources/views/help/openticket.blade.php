{{-- User: view tickets --}}
@extends('layout')
@section('title', 'New Ticket')

@section('content')
    <div class="container">
        <div class="row">
            <h3>Open Support Ticket</h3>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-info">Most common questions have already been answered in our FAQ.  Have you looked at our <a href="/help/kb">Knowledgebase</a> for an answer already? You're more likely to get your answer much more quickly by consulting the FAQ.</div>
                </div>
            </div>
            <form class="form-horizontal" action="/help/ticket/new" method="POST">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="form-group">
                    <label for="tSubject" class="col-sm-2 control-label">Subject</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="tSubject" id="tSubject"
                               placeholder="Ticket Subject">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tFacility" class="col-sm-2 control-label">Facility</label>
                    <div class="col-sm-10">
                        <select name="tFacility" id="tFacility" class="form-control">
                            <option value="ZHQ">VATUSA Headquarters</option>
                            <option value="ZAE">VATUSA Academy</option>
                            @foreach(\App\Facility::where('active', '1')->orderBy('name')->get() as $f)
                                <option value="{{$f->id}}">{{$f->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isVATUSAStaff())
                <div class="form-group">
                    <label for="tAssign" class="col-sm-2 control-label">Assign To</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="tAssign" id="tAssign">
                            <option value="0">Unassigned</option>
                            @foreach(\App\Classes\RoleHelper::getStaff("ZHQ", true) as $s)
                                <option value="{{$s['cid']}}">{{$s['role']}}
                                    : {{$s['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="form-group">
                    <label for="tMessage" class="col-sm-2 control-label">Message</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="5" id="tMessage" name="tMessage"
                                  placeholder="Ticket Message"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Open Ticket</button>
                ... or check the <a href="/help/kb">Knowledgebase</a>
            </form>
        </div>
    </div>
    @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isVATUSAStaff())
    <script type="text/javascript">
        $('#tFacility').change(function() {
            if ($('#tFacility').val() == "ZAE") {
                $('#tAssign').replaceOptions([{text: "Facility", value: 0}]);
            } else {
                $('#tAssign').replaceOptions([{text: "Loading", value: 0}]);
                $('#tAssign').prop('disabled', 'disabled');
                $.ajax({
                    method: "GET",
                    url: '/ajax/help/staff/' + $('#tFacility').val()
                }).done(function(r) {
                    $('#tAssign').replaceOptions($.parseJSON(r));
                    $('#tAssign').prop('disabled', false);
                });
            }
        });
    </script>
    @endif
@endsection
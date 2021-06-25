@extends('layout')
@section('title', 'Knowledgebase')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12"><h3>VATUSA Support Knowledgebase</h3></div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-right">Cannot find an answer? <a href="/help/ticket/new">Submit a support
                    ticket</a></div>
        </div>
        @foreach(\App\Models\KnowledgebaseCategories::get() as $cat)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        {{$cat->name}}
                    </h3>
                </div>
                <table class="table table-response">
                    @foreach($cat->questions()->orderBy('order','asc')->get() as $q)
                        <tr id="rowq{{$q->id}}">
                            <td>
                                <a href="#q{{$q->id}}" data-toggle="collapse" data-target="#q{{$q->id}}">{{$q->order}}
                                    . {{$q->question}}</a>
                                <div id="q{{$q->id}}" class="collapse">
                                    <p>{!! $q->answer !!}</p></div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endforeach
        <div class="row">
            <div class="col-lg-12 text-right">Cannot find an answer? <a href="/help/ticket/new">Submit a support
                    ticket</a></div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            if (window.location.hash) {
                anchor = window.location.hash.substring(1);
                $('#' + anchor).toggle();
                $('#' + anchor).addClass("highlight");
                $('#row' + anchor).addClass("highlight");
            }
        });
    </script>
@stop
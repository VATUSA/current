@extends('layout')
@section('title', 'TMU Map Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                @if(\App\Classes\RoleHelper::isVATUSAStaff())
                    <select id="fac" class="mgt-sel">
                        @foreach(\App\Facility::where('active', 1)->orderBy('name')->get() as $f)
                            <option name="{{$f->id}}" @if($f->id == $fac) selected="true" @endif>{{$f->id}}</option>
                        @endforeach
                    </select>&nbsp;-&nbsp;
                @endif
                    {{$facname}} TMU Map Color Management
                </h3>
            </div>
            <div class="panel-body">
              <form class='form-horizontal' method="post">
                <p>In each box below, list applicable airport IDs (ICAO) with comma delimiters (ie: KMSP,KSTP,KFCM).  If a color is not desired, leave it blank.  Do not add spaces between airports.  To set a default color, enter "default" (without quotes) into the textbox next to the desired color.  Unless set, the default color will be black <img src="/img/tmu/planes/black.png">.  *When in dark mode, black is replaced with white.</p>
                <?php $appcolors = ['black','brown','blue','gray','green','lime','cyan','orange','red','purple','yellow','violet']; ?>
                <?php foreach ($appcolors as $ac) { ?>
                <div class='form-group'>
                  <label for='<?=$ac?>' class='col-sm-2 control-label'>
                    <?=ucfirst($ac)?> <img src="/img/tmu/planes/<?=$ac?>.png">
                    @if($ac == "black")
                    <br>or white* <img src="/img/tmu/planes/white.png">
                    @endif
                  </label>
                  <div class='col-sm-10'>
                    <textarea name='<?=$ac?>' class='form-control'><?=$colors->{$ac}?></textarea>
                  </div>
                </div>
                <?php } ?>
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Save</button>
                  </div>
                </div>
              </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#fac').change(function() {
                window.location = "/mgt/tmu/" + $('#fac').val() + '/colors';
            });
        });
    </script>
@stop

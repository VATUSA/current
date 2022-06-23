{{-- User: send feedback --}}
@extends('layout')
@section('title', 'Send Feedback')

@section('content')
    <div class="container">
        <div class="row">
            <h3>Send Feedback</h3>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-info">Use the form below to leave feedback for ARTCC  
                        staff. Feedback helps us get better - thanks for taking a few minutes to 
                        tell us what's on your mind.
                    </div>
                </div>
            </div>
            <form class="form-horizontal" action="{{ secure_url('/info/feedback/new') }}" method="POST" id="sendfeedback-form">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="form-group">
                    <label for="fName" class="col-sm-2 control-label">Your Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="fName" id="fName"
                               placeholder="Your Name Here">
                    </div>
                </div>
                <div class="form-group">
                    <label for="fEmail" class="col-sm-2 control-label">Your Email Address</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="fEmail" id="fEmail"
                               placeholder="Your Email Address (optional, if follow-up is needed)">
                    </div>
                </div>
                <div class="form-group">
                    <label for="fGradingScale" class="col-sm-2 control-label">How'd We Do?</label>
                    <div class="col-sm-10">
                        <select name="fGradingScale" id="fGradingScale" class="form-control">
                            <option value="5">Excellent</option>
                            <option value="4">Above Average</option>
                            <option value="3">Average</option>
                            <option value="2">Below Average</option>
                            <option value="1">Poor</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fFacility" class="col-sm-2 control-label">Facility</label>
                    <div class="col-sm-10">
                        <select name="fFacility" id="fFacility" class="form-control">
                            @foreach(\App\Models\Facility::where('active', '1')->orderBy('name')->get() as $f)
                                <option value="{{$f->id}}">{{$f->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fMessage" class="col-sm-2 control-label">Feedback Message</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="5" id="fMessage" name="fMessage"
                                  placeholder="What's on your mind?"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary col-sm-offset-2" id="sendfeedback" data-loading-text="Submitting..."><i
                        class="fa fa-envelope-o"></i> Send Feedback
                </button>
            </form>
        </div>
    </div>
    <script type="text/javascript">
      $(document).ready(function () {
        $('#sendfeedback').on('click', function (e) {
          e.preventDefault()
          let btn  = $(this),
              form = $('#sendfeedback-form')
          btn.html('<i class=\'fa fa-spinner fa-spin\'></i> Submitting...').attr('disabled', true)
          form.submit()
        })
      })
    </script>
@endsection
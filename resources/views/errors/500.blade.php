@extends('layout')
@section('title', 'Error')

@section('content')
    <div class="container">
        <br>
        <div class="row">
            <div class="alert alert-warning">
                <div class="row">
                    <div class="col-md-2"><img src="/img/hal.png" style="width: 100%"></div>
                    <div class="col-md-10">I'm sorry, Dave. I'm afraid I can't do that.<br>
                    @if(app()->bound('sentry') && !empty(Sentry::getLastEventID()))
                        <!-- Sentry JS SDK 2.1.+ required -->
                            <script src="https://cdn.ravenjs.com/3.3.0/raven.min.js"></script>

                            <script>
                              Raven.showReportDialog({
                                eventId: '{{ Sentry::getLastEventID() }}',
                                dsn    : 'https://16986270d1eb40fc91ba23365d504a72@sentry.io/1361300',
                                user   : {
                                  'name' : 'Jane Doe',
                                  'email': 'jane.doe@example.com',
                                }
                              })
                            </script>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
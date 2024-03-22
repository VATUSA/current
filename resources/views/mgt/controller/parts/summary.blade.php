<div class="row">
    <div class="col-md-4">
        <ol style="font-size: 110%;list-style-type: none;">
            <li><strong>{{$user->fname}} {{$user->lname}}</strong></li>
            @if(\App\Helpers\AuthHelper::isVATUSAStaff() ||
                \App\Helpers\AuthHelper::isFacilitySeniorStaff())
                <li>{{$user->email}} &nbsp; <a href="mailto:{{$user->email}}"><i
                                class="fa fa-envelope text-primary"
                                style="font-size:80%"></i></a>
                </li>
            @else
                <li>[Email Private] <a href="/mgt/mail/{{$user->cid}}"><i
                                class="fa fa-envelope text-primary"></i></a></li>
            @endif
            <li>
                @if($user->flag_broadcastOptedIn)
                    <p class="text-success"><i class="fa fa-check"></i> Receiving Broadcast
                        Emails</p>
                @else
                    <p class="text-danger"><i class="fas fa-times"></i> Not Receiving
                        Broadcast
                        Emails
                    </p>
                @endif

            </li>
            <li>{{$user->urating->short}} - {{$user->urating->long}}</li>
            <li>Last promoted {{$user->lastPromotion()->created_at ?? 'never.'}}</li>
            <br>
            <li>{{$user->facility}}
                - {{\App\Classes\Helper::facShtLng($user->facility)}}</li>
            <li>Member of {{$user->facility}} since {{ $user->facility_join }}</li>
            @if($user->facility()->active)
                <li>
                    Training Staff?
                    @if (\App\Classes\RoleHelper::isInstructor($user->cid, $user->facility, false))
                        Instructor
                    @elseif(\App\Classes\RoleHelper::isMentor($user->cid, $user->facility))
                        Mentor
                    @else
                        No
                    @endif
                </li>
            @endif
            <br>
            @if ($user->visits()->exists())
                <li>Visiting:</li>
                @foreach ($user->visits()->get() as $visit)
                    <li>
                        {{$visit->fac->id}} - {{$visit->fac->name}}
                        @if(\App\Classes\RoleHelper::isTrainingStaff($user->cid, true, $visit->fac->id, false))
                            <br>
                            <span style="margin-left:2em;">
                                    @if (\App\Classes\RoleHelper::isInstructor($user->cid, $visit->fac->id, false))
                                    Instructor
                                @elseif(\App\Classes\RoleHelper::isMentor($user->cid, $visit->fac->id))
                                    Mentor
                                @endif
                                </span>
                        @endif
                    </li>
                @endforeach
                <br>
            @endif
            <li>Last Website Activity: {{$user->lastActivityWebsite()}} days ago</li>
            <br>
            <li>Needs Basic ATC or RCE:
                @if (\App\Helpers\AuthHelper::isVATUSAStaff())
                    <a href="/mgt/controller/{{$user->cid}}/togglebasic">
                        @endif
                        @if ($user->flag_needbasic)
                            Yes
                        @else
                            No
                        @endif
                        @if (\App\Helpers\AuthHelper::isVATUSAStaff())
                    </a>
                @endif
            </li>
            <br>
            @if (\App\Helpers\AuthHelper::isVATUSAStaff() &&
                $user->rating >= \App\Classes\Helper::ratingIntFromShort("OBS") &&
                $user->rating < \App\Classes\Helper::ratingIntFromShort("SUP"))
                <li>Rating Change
                    <select id="ratingchange">
                        @foreach (\App\Models\Rating::get() as $rating)
                            @if(in_array($rating->id, [1,2,3,4,5,7,8,10]))
                                <option
                                        @if ($user->rating == $rating->id) selected @endif
                                        value="{{$rating->id}}">{{$rating->short}} - {{$rating->long}}</option>
                            @endif
                        @endforeach
                    </select>
                    <div class="alert alert-danger" id="ratingchange-warning"
                         style="display:none;"><strong><i class="fas fa-times"></i>
                            Warning!</strong> This controller currently has the Prevent
                        Staff Role Assignment flag.
                    </div>
                    <button class="btn btn-info" id="ratingchangebtn">Save</button>
                    <span class="" id="ratingchangespan"></span></li>
                <script type="text/javascript">
                    $('#ratingchangebtn').click(function () {
                        $('#ratingchangespan').html('Saving...')
                        $.ajax({
                            url: '/mgt/controller/{{$user->cid}}/rating',
                            type: 'POST',
                            data: {rating: $('#ratingchange').val()}
                        }).success(function () {
                            $('#ratingchangespan').html('Saved')
                            setTimeout(function () {
                                $('#ratingchangespan').html('')
                            }, 3000)
                        })
                            .error(function () {
                                $('#ratingchangespan').html('Error')
                                setTimeout(function () {
                                    $('#ratingchangespan').html('')
                                }, 3000)
                            })
                    })
                </script>
            @endif
        </ol>
    </div>
    <div class="col-md-8" style="border-left: 1px solid #ccc6;">
        <table class="table table-responsive table-striped">
            <thead>
            <tr>
                <th style="width:100%;">Transfer eligibility checks</th>
                <th>Pass/Fail</th>
            </tr>
            </thead>
            <tr>
                <td>In VATUSA division?</td>
                <td>{!! ($checks['homecontroller'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
            </tr>
            <tr>
                <td>Needs to complete the Basic ATC/S1 courses or RCE Exam?</td>
                <td>{!! ($checks['needbasic'])?'<span class="text-success">No</span>':'<span class="text-danger">Yes</span>' !!}</td>
            </tr>
            <tr>
                <td>90 days since last transfer?</td>
                @if($checks['is_first'] == 1)
                    <td><span class="text-success">N/A</span></td>
                @elseif($checks['90days'])
                    <td><i class="fa fa-check text-success"></i></td>
                @else
                    <td><i class="fa fa-times text-danger"></i>{!! "(".$checks['days']." days)" !!}</td>
                @endif
            </tr>
            <tr>
                <td>In first facility?</td>
                @if($checks['is_first'] == 0)
                    <td><span class="text-success">N/A</span></td>
                @elseif($checks['initial'] == 1)
                    <td><i class="fa fa-check text-success"></i></td>
                @else
                    <td><i class="fa fa-times text-danger"></i></td>
                @endif
            </tr>
            <tr>
                <td>90 days since promotion to S1, S2, S3, or C1?</td>
                <td>{!! ($checks['promo'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>('.$checks['promoDays'].' days)' !!}</td>
            </tr>
            <tr>
                <td>50 controlling hours since promotion to S1, S2, S3, or C1?</td>
                @if($checks['hasHome'] == 0)
                    <td><span class="text-success">N/A</span></td>
                @elseif($checks['50hrs'] == 1)
                    <td><i class="fa fa-check text-success"></i></td>
                @else
                    <td><i class="fa fa-times text-danger"></i>{!! "(".$checks['ratingHours']." hours)" !!}</td>
                @endif
            </tr>
            <tr>
                <td>Does not hold a staff position at a facility?</td>
                <td>{!! ($checks['staff'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
            </tr>
            <tr>
                <td>Does not hold an I1 or I3 rating?</td>
                <td>{!! ($checks['instructor'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
            </tr>
            <tr>
                <td>Does not have pending transfers?</td>
                <td>{!! ($checks['pending'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
            </tr>
            <tr>
                <td>Has a transfer waiver?</td>
                <td>{!! ($checks['override'])?'<i class="fa fa-check text-success"></i>':'<span class="text-success">N/A</span>' !!}</td>
            </tr>
            <tr>
                <td>Are they eligible?</td>
                <td>{!! ($eligible)?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
            </tr>
        </table>

        <table class="table table-responsive table-striped">
            <thead>
            <tr>
                <th style="width:100%;">Visiting eligibility checks</th>
                <th>Pass/Fail</th>
            </tr>
            </thead>
            <tr>
                <td>Has a home facility?</td>
                <td>{!! ($checks['hasHome'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
            </tr>
            <tr>
                <td>Needs to complete RCE Exam?</td>
                <td>{!! ($checks['needbasic'])?'<span class="text-success">No</span>':'<span class="text-danger">Yes</span>' !!}</td>
            </tr>
            <tr>
                <td>Has at least an S3 rating?</td>
                <td>{!! ($checks['hasRating'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
            </tr>
            <tr>
                <td>60 days since last addition to a visiting roster?</td>
                <td>{!! ($checks['60days'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>('.$checks['visitingDays'].' days)' !!}</td>
            </tr>
            <tr>
                <td>90 days since promotion to S1, S2, S3, or C1?</td>
                <td>{!! ($checks['promo'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>('.$checks['promoDays'].' days)' !!}</td>
            </tr>
            <tr>
                <td>50 controlling hours since promotion to S1, S2, S3, or C1?</td>
                <td>{!! ($checks['50hrs'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>('.$checks['ratingHours'].' hours)' !!}</td>
            </tr>
            <tr>
                <td>Are they eligible?</td>
                <td>{!! ($checks['visiting'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
            </tr>
        </table>
    </div>
</div>
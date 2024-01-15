<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Exam History
        </h3>
    </div>
    <div class="panel-body">
        <div>
            @if (\App\Classes\RoleHelper::isFacilitySeniorStaff()
                || \App\Classes\RoleHelper::isInstructor(Auth::user()->cid)
                || \App\Classes\RoleHelper::isMentor(Auth::user()->cid))
                <div style="text-align: center;">
                    <a href="https://academy.vatusa.net/grade/report/overview/index.php?id=19&userid={{$moodleUid}}&userview=1"
                       style="text-decoration: none; font-size: 24px; "
                       target="_blank"><span
                                class="label label-success"><i
                                    class="fas fa-check"
                                    style="font-size: inherit !important;"></i> View Grades in Academy</span></a>
                </div>
            @endif
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Exam Name</th>
                    <th>Attempts</th>
                    <th>Enrollment</th>
                </tr>
                </thead>
                <tbody>
                @foreach($examAttempts as $exam => $data)
                    @php $hasPassed = 0; @endphp
                    <tr @if($data['examInfo']['rating'] - 1 > $user->rating) class="text-muted" @endif>
                        <td>{{ $exam }}</td>
                        <td>@if(empty($data['attempts']) && $data['examInfo']['rating'] - 1 <= $user->rating)
                                <span
                                        class="label label-info"><i
                                            class="fas fa-question-circle"
                                            style="font-size: inherit !important;"></i> Not Taken</span>
                            @elseif(empty($data['attempts']))
                                <em>Not Eligible</em>
                            @else
                                @foreach($data['attempts'] as $attempt)
                                    <p>Attempt
                                        <strong>{{ $attempt['attempt'] }}</strong>:
                                        @switch($attempt['state'])
                                            @case('finished')
                                                @if(round($attempt['grade'] >= $data['examInfo']['passingPercent']))
                                                    @php $hasPassed = 1; @endphp
                                                    <a href="https://academy.vatusa.net/mod/quiz/review.php?attempt={{$attempt['id']}}"
                                                       style="text-decoration: none"
                                                       target="_blank"><span
                                                                class="label label-success"><i
                                                                    class="fas fa-check"
                                                                    style="font-size: inherit !important;"></i> Passed ({{ $attempt['grade'] }}%)</span></a>
                                                @else
                                                    <a href="https://academy.vatusa.net/mod/quiz/review.php?attempt={{$attempt['id']}}"
                                                       style="text-decoration: none"
                                                       target="_blank"><span
                                                                class="label label-danger"><i
                                                                    class="fas fa-times"
                                                                    style="font-size: inherit !important;"></i> Failed ({{ $attempt['grade'] }}%)</span></a>
                                                @endif
                                                @break
                                            @case('inprogress')
                                                <span class="label label-warning"><i
                                                            class="fas fa-clock"
                                                            style="font-size: inherit !important;"></i> In Progress</span>
                                                @break
                                            @default
                                                <span
                                                        class="label label-danger">{{ ucwords($attempt['state']) }}</span>
                                                @break
                                        @endswitch
                                        <br>
                                    </p>
                                @endforeach
                            @endif
                        </td>
                        <td @if($data['examInfo']['id'] != config('exams.BASIC.id')) id="enrollment-status-{{ $data['examInfo']['courseId'] }}" @endif>
                            @if($hasPassed)
                                <strong style="color: #39683a"><em><i
                                                class="fas fa-check-double"></i> Course
                                        Complete</em></strong>
                            @elseif ($data['assignDate'])
                                <strong class="text-success"><i
                                            class="fas fa-user-check"></i>
                                    Enrolled</strong>
                                on
                                {{ $data['assignDate'] }}
                            @elseif($data['examInfo']['id'] == config('exams.BASIC.id') || $data['examInfo']['rating'] <= $user->rating)
                                <em>Auto-Enrolled</em>
                            @elseif($data['examInfo']['rating'] - 1 == $user->rating)
                                @if(\App\Classes\RoleHelper::isMentor(Auth::user()->cid, $user->facility) && $data['examInfo']['rating'] <= Auth::user()->rating ||
                                      !\App\Classes\RoleHelper::isMentor(Auth::user()->cid, $user->facility))
                                    <button
                                            class="btn btn-success btn-sm enrol-exam-course"
                                            data-id="{{ $data['examInfo']['courseId'] }}"
                                            data-name="{{ $exam }}">
                                        <i
                                                class="fas fa-user-plus"></i> Enroll
                                    </button>
                                @else
                                    <span
                                            class="label label-danger"><i
                                                class="fas fa-times-circle"
                                                style="font-size: inherit !important;"></i> Not Enrolled</span>
                                @endif
                            @else
                                <em>Not Eligible</em>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
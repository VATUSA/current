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
        <tr @if($data['examInfo']['rating'] - 1 > Auth::user()->rating) class="text-muted" @endif>
            <td>{{ $exam }}</td>
            <td>@if(empty($data['attempts']) && $data['examInfo']['rating'] - 1 <= Auth::user()->rating)
                    <span
                        class="label label-info"><i class="fas fa-question-circle"
                                                    style="font-size: inherit !important;"></i> Not Taken</span>
                @elseif(empty($data['attempts']))
                    <em>Not Eligible</em>
                @else
                    @foreach($data['attempts'] as $attempt)
                        <p>Attempt
                            <strong>{{ $attempt['attempt'] }}</strong>:
                            @switch($attempt['state'])
                                @case('finished')
                                @if($attempt['grade'] >= $data['examInfo']['passingPercent'])
                                    @php $hasPassed = 1; @endphp
                                    <a href="https://academy.vatusa.net/mod/quiz/review.php?attempt={{$attempt['id']}}" style="text-decoration: none" target="_blank"><span
                                            class="label label-success"><i class="fas fa-check" style="font-size: inherit !important;"></i> Passed ({{ $attempt['grade'] }}%)</span></a>
                                @else
                                    <a href="https://academy.vatusa.net/mod/quiz/review.php?attempt={{$attempt['id']}}" style="text-decoration: none" target="_blank"><span
                                            class="label label-danger"><i class="fas fa-times" style="font-size: inherit !important;"></i> Failed ({{ $attempt['grade'] }}%)</span></a>
                                @endif
                                @break
                                @case('inprogress')
                                <span class="label label-warning"><i class="fas fa-clock"
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
            <td>
                @if($hasPassed)
                    <strong style="color: #39683a"><em><i
                                class="fas fa-check-double"></i> Course Complete</em></strong>
                @elseif ($data['assignDate'])
                    <strong class="text-success"><i
                            class="fas fa-user-check"></i> Enrolled</strong>
                    on
                    {{ $data['assignDate'] }}
                @elseif($data['examInfo']['id'] === config('exams.BASIC.id'))
                    <em>Auto-Enrolled</em>
                @elseif($data['examInfo']['rating'] - 1 <= Auth::user()->rating)
                    <span
                        class="label label-danger"><i class="fas fa-times-circle"
                                                      style="font-size: inherit !important;"></i> Not Enrolled</span>
                @else
                    <em>Not Eligible</em>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
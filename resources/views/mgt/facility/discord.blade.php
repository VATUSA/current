<div class="panel panel-default">
    <div class="panel-body">
        <div class="text-center" style="margin-bottom:10px">
            <a href="https://discord.com/oauth2/authorize?client_id={{ config('services.discord.client_id') }}&scope=bot&permissions={{ config('services.discord.botPermissions') }}"
               target="_blank">
                <button class="btn btn-primary" style="margin:0 auto"><i
                        class="fas fa-plus"></i> Add Bot to Server
                </button>
            </a>
        </div>
        <form class="form" method="POST" action="{{ secure_url("/mgt/facility/$fac/discord") }}">
            @csrf
            <div class="form-group row" style="display: flex; justify-content: center">
                <label for="guild" class="col-md-2 control-label">Facility Discord
                    Server</label>
                <div class="col-md-5">
                    <select class="form-control" name="guild" id="guild" autocomplete="off">
                        <option value="0">--- None ---</option>
                        @foreach($userGuilds as $guild)
                            <option value="{{ $guild['id'] }}"
                                    @if($facility->discord_guild === $guild['id']) selected @endif> {{ $guild['name'] }}</option>
                        @endforeach
                    </select>
                    <p class="help-block">Only servers of which the bot is a member and
                        that
                        your linked account has the <code>Manage Server</code>
                        permission
                        will be shown.</p>
                </div>
            </div>
            <div class="form-group row" style="display: flex; justify-content: center">
                <div class="col-sm-offset-2 col-sm-5">
                    <input id="current-guild" value="{{ $facility->discord_guild }}" type="hidden">
                    <button type="submit" class="btn btn-success" id="submit-guild" disabled><i
                            class="fas fa-check"></i> @if(!$facility->discord_guild) Set @else Change @endif Server
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<table class="table table-striped table-responsive" id="channel-notifications-table">
    <thead>
    <tr>
        <th>Notification Type</th>
        <th><i class="fas fa-hashtag"></i> Channel</th>
    </tr>
    </thead>
    <tbody>
    <tr class="notification-group">
        <td><strong><i class="fas fa-graduation-cap"></i> Academy/Legacy Exams</strong>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>Academy Exam Course Enrolled</td>
        @php $val = $notificationChannels['academy_exam_course_enrolled'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="academy_exam_course_enrolled" class="form-control notification-channel-select"
                         autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @php $currentGroup = 0; @endphp
                    @for($i = 0; $i < count($guildChannels); $i++)
                    @php $channel = $guildChannels[$i]; @endphp
                    @if($channel['parentId'] && $channel['parentId'] !== $currentGroup)
                    @php $currentGroup = $channel['parentId']; @endphp
                    @if($guildChannels[$i + 1]['parentId'] ?? 0 != $currentGroup)
                    </optgroup>
                    @endif
                    <optgroup label="{{ $channel['parentName'] }}">
                        @endif
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endfor
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Academy Exam Result</td>
        @php $val = $notificationChannels['academyExamResult'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="academyExamResult" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Legacy Exam Assigned</td>
        @php $val = $notificationChannels['legacyExamAssigned'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="legacyExamAssigned" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Legacy Exam Result</td>
        @php $val = $notificationChannels['legacyExamResult'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="legacyExamResult" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr class="notification-group">
        <td><strong><i class="fas fa-users"></i> Roster Membership</strong></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>New Transfer Request</td>
        @php $val = $notificationChannels['transferNew'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="transferNew" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Transfer Accepted/Denied</td>
        @php $val = $notificationChannels['transferAction'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="transferAction" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Roster Removal</td>
        @php $val = $notificationChannels['rosterRemoval'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="rosterRemoval" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Pending Transfers (more than {{ config('tattlers.transfers.maxdays', 7) }} days)</td>
        @php $val = $notificationChannels['transferPending'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="transferPending" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr class="notification-group">
        <td><strong><i class="fas fa-life-ring"></i> Support Tickets</strong></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>New Ticket</td>
        @php $val = $notificationChannels['ticketNew'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="ticketNew" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Ticket Assigned to You</td>
        @php $val = $notificationChannels['ticketAssigned'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="ticketAssigned" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Ticket Reply</td>
        @php $val = $notificationChannels['ticketReply'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="ticketReply" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Ticket Reopened</td>
        @php $val = $notificationChannels['ticketReopened'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="ticketReopened" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>Ticket Closed</td>
        @php $val = $notificationChannels['ticketClosed'] ?? 0; @endphp
        <td class="notification-setting-cell">
            <div><select name="ticketClosed" class="form-control notification-channel-select" autocomplete="off"
                         @if(!$guildChannels) disabled @endif>
                    <option value="0" @if(!$val) selected @endif>--- None ---</option>
                    @foreach($guildChannels as $channel)
                        <option value="{{ $channel['id'] }}"
                                @if($val === $channel['id']) selected @endif>{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    </tbody>
</table>
@push('scripts')
    <script type="text/javascript">
      $(document).ready(function () {
        $('#guild').change(function () {
          $('#submit-guild').prop('disabled', !parseInt($(this).val()) || $(this).val() === $('#current-guild').val())
        })
        $('.notification-channel-select').change(function () {
          let input = $(this),
              val   = input.val(),
              type  = input.attr('name')
          if (!val.length) return false
          input.prop('disabled', true)
          $.post("/mgt/facility/{{$fac}}/ajaxDiscordNotificationChannel", {
            type   : type,
            channel: val
          }).fail(_ => swal('Error!', 'Unable to update channel. Please try again later.', 'error')).always(_ =>
            input.prop('disabled', false)
          )
        })
      })
    </script>
@endpush
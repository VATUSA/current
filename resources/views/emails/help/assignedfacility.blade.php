Hello,

<p>A support ticket has been assigned to your facility.  Details:</p>

<p>ID: {{$ticket->id}}
    Submitter: {{$ticket->submitter->fullname()}} ({{$ticket->cid}})<br>
    Subject: {{$ticket->subject}}<br>
    Facility: {{$ticket->facility}}</p>

<p>To view ticket or reply, go to https://www.vatusa.net/help/ticket/{{$ticket->id}}</p>

<p>Sincerely,<br>
    VATUSA Web Services</p>
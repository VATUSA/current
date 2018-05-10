Hello,

<p>A new support ticket has been submitted.  Details:</p>

<p>ID: {{$ticket->id}}<br>
Submitter: {{$ticket->submitter->fullname()}} ({{$ticket->cid}})<br>
Subject: {{$ticket->subject}}<br>
Facility: {{$ticket->facility}}</p>

<p>To view ticket or reply, go to https://www.vatusa.net/help/ticket/{{$ticket->id}}</p>

<p>Sincerely,<br>
VATUSA Web Services</p>

<p>---<br>
REMINDER: VATUSA staff will never ask you for your VATSIM password. Do *not* disclose it.</p>
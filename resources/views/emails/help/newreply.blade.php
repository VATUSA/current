Hello,

<p>A new reply to a support ticket has been submitted.  Details:</p>

<p>ID: {{$ticket->id}}<br>
Subject: {{$ticket->subject}}<br>
Facility: {{$ticket->facility}}<br>
Assigned To: {{($ticket->assigned_to)? $ticket->assignedto->fullname() : "Unassigned"}}<br>
Status: {{$ticket->status}}</p>

<p>
Reply from: {{$reply->submitter->fullname()}}
<br>
Reply: {{$reply->body}}
</p>

<p>To view ticket or reply, go to https://www.vatusa.net/help/ticket/{{$ticket->id}}</p>

<p>Sincerely,<br>
VATUSA Web Services</p>

<p>---<br>
REMINDER: VATUSA staff will never ask you for your VATSIM password. Do *not* disclose it.</p>
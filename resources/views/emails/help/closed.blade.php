Hello,

<p>Your ticket has been marked as closed by {{$closer}}.  Details:</p>

<p>ID: {{$ticket->id}}<br>
    Subject: {{$ticket->subject}}<br>
    Facility: {{$ticket->facility}}<br>
    Assigned To: {{($ticket->assigned_to)? $ticket->assignedto->fullname() : "Unassigned"}}<br>
    Status: {{$ticket->status}}</p>

<p>To view ticket, go to https://www.vatusa.net/help/ticket/{{$ticket->id}}</p>

<p>Sincerely,<br>
    VATUSA Web Services</p>

<p>---<br>
    REMINDER: VATUSA staff will never ask you for your VATSIM password. Do *not* disclose it.</p>
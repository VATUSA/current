@extends('emails.layout')
@section('title','Ticket Closed')
@section('content')
    Hello,

    <p>Your ticket has been marked as closed by {{$closer}}. </p>

    <p>Subject: {{$ticket->subject}}<br>
        Facility: {{$ticket->facility}}<br>
        Assigned To: {{($ticket->assigned_to)? $ticket->assignedto->fullname() : "Unassigned"}}<br></p>


    <table class="button warning"
           style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: auto;">
        <tbody>
        <tr style="padding: 0; text-align: left; vertical-align: top;">
            <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                <table
                    style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top;">
                    <tbody>
                    <tr style="padding: 0; text-align: left; vertical-align: top;">
                        <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #ffae00; border: 0px solid #ffae00; border-collapse: collapse !important; color: #fefefe; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                            <a href="https://www.vatusa.net/help/ticket/{{$ticket->id}}"
                               style="Margin: 0; border: 0px solid #ffae00; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 1.3; margin: 0; padding: 8px 16px 8px 16px; text-align: left; text-decoration: none;">Reopen
                                Ticket ⇾</a></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
@endsection
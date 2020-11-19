@extends('emails.layout')
@section('title','Ticket Reply')
@section('content')
    Hello,

    <p>A new reply to a support ticket has been submitted.</p>

    <p>Subject: {{$ticket->subject}}<br>
        Facility: {{$ticket->facility}}<br>
        Assigned To: {{($ticket->assigned_to)? $ticket->assignedto->fullname() : "Unassigned"}}<br>
        Status: {{$ticket->status}}</p>

    <p>
        Reply from: {{$reply->submitter->fullname()}}
        <table class="callout"
               style="Margin-bottom: 16px; border-collapse: collapse; border-spacing: 0; margin-bottom: 16px; padding: 0; text-align: left; vertical-align: top;">
            <tbody>
            <tr style="padding: 0; text-align: left; vertical-align: top;">
                <th class="callout-inner light-gray"
                    style="Margin: 0; background: #fefefe; border: 1px solid #cbcbcb; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 10px; text-align: left; width: 100%;">
                    <table class="row"
                           style="border-collapse: collapse; border-spacing: 0; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                        <tbody>
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                            <th class="small-12 large-12 columns first last"
                                style="Margin: 0 auto; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 16px; padding-right: 16px; text-align: left; width: 564px;">
                                <table
                                    style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                                    <tbody>
                                    <tr style="padding: 0; text-align: left; vertical-align: top;">
                                        <th style="Margin: 0; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;">
    <p style="Margin: 0; Margin-bottom: 10px; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; margin-bottom: 10px; padding: 0; text-align: left;">{{ $reply->body }}</p>
    </th>
    <th class="expander"
        style="Margin: 0; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
    </tr>
    </tbody>
    </table>
    </th>
    </tr>
    </tbody>
    </table>
    </th>
    <th class="expander"
        style="Margin: 0; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th>
    </tr>
    </tbody>
    </table>
    </p>

    <table class="button success float-center" align="center"
           style="border-collapse: collapse; border-spacing: 0; float: none; padding: 0; text-align: center; vertical-align: top; width: auto;">
        <tbody>
        <tr style="padding: 0; text-align: left; vertical-align: top;">
            <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                <table
                    style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top;">
                    <tbody>
                    <tr style="padding: 0; text-align: left; vertical-align: top;">
                        <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #3adb76; border: 0px solid #3adb76; border-collapse: collapse !important; color: #fefefe; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                            <a href="https://www.vatusa.net/help/ticket/{{$ticket->id}}"
                               style="Margin: 0; border: 0 solid #3adb76; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 1.3; margin: 0; padding: 8px 16px 8px 16px; text-align: left; text-decoration: none;">View
                                Ticket ⇾</a></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <p>---<br>
        <em>REMINDER: VATUSA staff will never ask you for your VATSIM password. Do *not* disclose it.</p>
@endsection
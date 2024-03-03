<?php
namespace App\Classes;

use Mail;

class EmailHelper
{
    /**
     * Send Email, checking for facility template first
     *
     * @param string|array $email
     * @param string $subject
     * @param string $template
     * @param array $data
     */
    public static function sendEmailFacilityTemplate($email, $subject, $fac, $template, $data)
    {
        $global_templates = [
            'examassigned' => "emails.exam.assign",
            'exampassed' => 'emails.exam.passed',
            'examfailed' => 'emails.exam.failed',
            'transferpending' => 'emails.transfers.pending'
        ];
        if (view()->exists("emails.facility.$fac." . $template)) {
            $template = "emails.facility.$fac.$template";
        } else {
            $template = $global_templates[$template];
        }

        static::sendEmail($email, $subject, $template, $data);
    }

    /**
     * Send an email from support
     *
     * @param string|array $email
     * @param string $subject
     * @param string $template
     * @param array $data
     */

    public static function sendSupportEmail($email, $ticket, $subject, $template, $data)
    {
//        $backup = Mail::getSwiftMailer();
//        $transport = new \Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_ENCRYPTION', 'tls'));
//        $transport->setUsername(env("SUPPORT_EMAIL_USERNAME"));
//        $transport->setPassword(env("SUPPORT_EMAIL_PASSWORD"));
//        $support = new \Swift_Mailer($transport);
//        Mail::setSwiftMailer($support);
        if (is_array($email)) {
            $sent = [];
            foreach($email as $e) {
                if (in_array($e, $sent)) continue;
                try {
                    Mail::send($template, $data, function($msg) use ($data, $e, $subject, $ticket) {
                        $msg->from('support@vatusa.net', 'VATUSA Help Desk');
                        $msg->bcc($e);
                        $msg->subject("[VATUSA Help Desk] (Ticket #$ticket) $subject");
                    });
                } catch (\Exception $exception) {
                    // Don't do anything - temporary workaround due to mail host failures
                }
                $sent[] = $e;
            }
        } else {
            try {
                Mail::send($template, $data, function($msg) use ($data, $email, $subject, $ticket) {
                    $msg->from('support@vatusa.net', 'VATUSA Help Desk');
                    $msg->bcc($email);
                    $msg->subject("[VATUSA Help Desk] (Ticket #$ticket) $subject");
                });
            } catch (\Exception $exception) {
                // Don't do anything - temporary workaround due to mail host failures
            }
        }
//        Mail::setSwiftMailer($backup);
    }

    /**
     * Send an email to one or more recipients.
     *
     * @param string|array $email
     * @param string $subject
     * @param string $template
     * @param array $data
     */
    public static function sendEmail($email, $subject, $template, $data)
    {
        try {
            Mail::send($template, $data, function ($msg) use ($data, $email, $subject) {
                $msg->from('no-reply@vatusa.net', "VATUSA Web Services");
                $msg->to($email);
                $msg->subject("[VATUSA] $subject");
            });
        } catch (\Exception $exception) {
            // Don't do anything - temporary workaround due to mail host failures
        }
    }

    public static function sendWelcomeEmail($email, $subject, $template, $data)
    {
        $welcome = $data['welcome'];
        $welcome = preg_replace("/%fname%/", $data['fname'], $welcome);
        $welcome = preg_replace("/%lname%/", $data['lname'], $welcome);
        static::sendEmail($email, $subject, $template, ['welcome' => $welcome]);
    }

    /**
     * Send email "to" from with emails array set as BCC.
     *
     * @param $fromEmail
     * @param $fromName
     * @param $emails
     * @param $subject
     * @param $template
     * @param $data
     */
    public static function sendEmailBCC($fromEmail, $fromName, $emails, $subject, $template, $data)
    {
        $emails = array_unique(array_merge($emails, [$fromEmail]));
        foreach($emails as $email) {
            try {
                Mail::send($template, $data, function ($msg) use ($data, $fromEmail, $email, $fromName, $subject) {
                    $msg->from("no-reply@vatusa.net", "VATUSA Web Services");
                    $msg->subject("[VATUSA] $subject");
                    $msg->bcc($email);
                });
            } catch (\Exception $exception) {
                // Don't do anything - temporary workaround due to mail host failures
            }
        }
    }

}

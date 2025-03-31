<?php
namespace App\Classes;

use Mail;
use Log;

class EmailHelper
{
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
                    Log::error("Failed to send support email to {$e} for ticket #{$ticket}: " . $exception->getMessage());
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
                Log::error("Failed to send support email to {$email} for ticket #{$ticket}: " . $exception->getMessage());
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
            Log::error("Failed to send email to {$email}: " . $exception->getMessage());
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
                Log::error("Failed to send email: " . $exception->getMessage());
            }
        }
    }

    /**
     * @param string $email
     * @param string $from_email
     * @param string $from_name
     * @param string $subject
     * @param string $template
     * @param array $data
     */
    public static function sendEmailFrom($email, $from_email, $from_name, $subject, $template, $data)
    {
        try {
            Mail::send($template, $data, function ($msg) use ($data, $from_email, $from_name, $email, $subject) {
                $msg->from("no-reply@vatusa.net", "$from_name");
                $msg->replyTo($from_email, $from_name);
                $msg->to($email);
                $msg->subject("[VATUSA] $subject");
            });
        } catch (\Exception $exception) {
            // Don't do anything - temporary workaround due to mail host failures
        }
    }

    /**
     * Check if user has opted in to broadcast emails.
     * @param $cid
     *
     * @return int
     */
    public static function isOptedIn($cid) {
        $user = \App\Models\User::find($cid);

        return $user && $user->flag_broadcastOptedIn;
    }
}

<?php
namespace App\Classes;

use Mail;
use App\EmailConfig;

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
        $backup = Mail::getSwiftMailer();
        $transport = new \Swift_SmtpTransport(env('MAIL_HOST'), 587, env('MAIL_ENCRYPTION', 'tls'));
        $transport->setUsername(env("SUPPORT_EMAIL_USERNAME"));
        $transport->setPassword(env("SUPPORT_EMAIL_PASSWORD"));
        $support = new \Swift_Mailer($transport);
        Mail::setSwiftMailer($support);
        if (is_array($email)) {
            $sent = [];
            foreach($email as $e) {
                if (in_array($e, $sent)) continue;
                Mail::send($template, $data, function($msg) use ($data, $e, $subject, $ticket) {
                    $msg->from('support@vatusa.net', 'VATUSA Help Desk');
                    $msg->bcc($e);
                    $msg->subject("[VATUSA Help Desk] (Ticket #$ticket) $subject");
                });
                $sent[] = $e;
            }
        } else {
            Mail::send($template, $data, function($msg) use ($data, $email, $subject, $ticket) {
                $msg->from('support@vatusa.net', 'VATUSA Help Desk');
                $msg->bcc($email);
                $msg->subject("[VATUSA Help Desk] (Ticket #$ticket) $subject");
            });
        }
        Mail::setSwiftMailer($backup);
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
        Mail::send($template, $data, function ($msg) use ($data, $email, $subject) {
            $msg->from('no-reply@vatusa.net', "VATUSA Web Services");
            $msg->to($email);
            $msg->subject("[VATUSA] $subject");
        });
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
        Mail::send($template, $data, function ($msg) use ($data, $fromEmail, $emails, $fromName, $subject) {
            $msg->from("no-reply@vatusa.net", "VATUSA Web Services");
            $msg->to($fromEmail, $fromName);
            $msg->subject("[VATUSA] $subject");
            $msg->bcc($emails);
        });
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
        Mail::send($template, $data, function ($msg) use ($data, $from_email, $from_name, $email, $subject) {
            $msg->from("no-reply@vatusa.net", "$from_name");
            $msg->replyTo($from_email, $from_name);
            $msg->to($email);
            $msg->subject("[VATUSA] $subject");
        });
    }

    /**
     * @param $address
     * @return int|string
     */
    public static function getType($address) {
        if(\DB::connection("email")->table("virtual_users")->where("email", $address)->count() < 1) {
            if (\DB::connection("email")->table("virtual_aliases")->where("source", $address)->count() < 1) {
                return -1;
            } else {
                return static::$email_forward;
            }
        }
        return static::$email_full;
    }

    /**
     * Check if user has opted in to broadcast emails.
     * @param $cid
     *
     * @return int
     */
    public static function isOptedIn($cid) {
        $user = \App\User::find($cid);

        return $user && $user->flag_broadcastOptedIn;
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function isStaticForward($email) {
        $email = EmailConfig::find($email);
        if (!$email) {
            \Log::critical("Missing email config $email in EmailHelper::isStaticForward()");
            return false;
        }

        return $email->isStatic();
    }

    /**
     * @param $address
     * @param $type
     * @param null $destination
     */
    public static function chgEmailConfig($address, $type, $destination = null) {
        $email = EmailConfig::find($address);
        if (!$email) { $email = new EmailConfig(); $email->address = $address; }

        if (!in_array($type, [EmailConfig::$configStatic, EmailConfig::$configUser])) {
            throw new Exception("Invalid type $type");
        }

        $email->config = $type;
        $email->destination = $destination;
        $email->modified_by = \Auth::user()->cid;
        $email->updated_at = \DB::raw("NOW()");
        $email->save();
    }

    /**
     * @param $email
     */
    private static function getDomainId($email) {
        $parts = explode("@", $email);
        $res = \DB::connection("email")->table("virtual_domains")->where("name", $parts[1])->first();
        if (!$res) { return; }
        return $res->id;
    }

    /**
     * @param $email
     * @param $password
     * @return int|void
     */
    public static function addEmail($email, $password) {
        $id = static::getdomainId($email);
        if (!$id) return;

        if (\DB::connection("email")->table("virtual_users")->where("email", $email)->count() > 0) {
            \Log::info("addEmail($email, ----) found duplicate email");
            return -1;
        }

        \DB::connection("email")->table("virtual_users")->insert([
            'domain_id' => $id,
            'email' => $email,
            'password' => crypt($password, '$6$' . substr(sha1(microtime()), -16))
        ]);
        return 1;
    }

    /**
     * @param $email
     * @param $password
     * @return int|void
     */
    public static function setPasswordEmail($email, $password) {
        $id = static::getdomainId($email);
        if (!$id) return;

        if (\DB::connection("email")->table("virtual_users")->where("email", $email)->count() != 1) {
            return -1;
        }

        \DB::connection("email")->table("virtual_users")->where('email', $email)->update([
            'password' => crypt($password, '$6$' . substr(sha1(microtime()), -16))
        ]);
        return 1;
    }

    /**
     * @param $email
     * @return int|void
     */
    public static function deleteEmail($email) {
        $id = static::getdomainId($email);
        if (!$id) return;

        if (\DB::connection("email")->table("virtual_users")->where("email", $email)->count() != 1) {
            return -1;
        }

        \DB::connection("email")->table("virtual_users")->where('email', $email)->delete();
        return 1;
    }

    /**
     * @param $source
     * @param $destination
     * @return null|integer
     */
    public static function setForward($source, $destination) {
        $ret = static::deleteForward($source);
        if (!$ret) return $ret;
        $ret = static::addForward($source, $destination);
        return $ret;
    }

    /**
     * @param string $source
     * @param string|array $destination
     * @return null|integer
     */
    public static function addForward($source, $destination) {
        $id = static::getdomainId($source);
        if (!$id) return;

        if (!is_array($destination) && strpos($destination, ",")) {
            $destination = explode(",", $destination);
        }

        if (is_array($destination)) {
            foreach($destination as $dest) {
                \DB::connection("email")->table("virtual_aliases")->insert(['domain_id'=>$id,'source'=>$source,'destination'=>$dest]);
            }
        } else {
            \DB::connection("email")->table("virtual_aliases")->insert(['domain_id'=>$id,'source'=>$source,'destination'=>$destination]);
        }
        return 1;
    }

    /**
     * @param $source
     * @param null $destination
     * @return null|integer
     */
    public static function deleteForward($source, $destination = null) {
        $id = static::getdomainId($source);
        if (!$id) return;

        $query = \DB::connection("email")->table("virtual_aliases")->where('source', $source);
        if ($destination) $query = $query->where('destination', $destination);
        $query->delete();
        return 1;
    }


    /**
     * @param $source
     * @return array|bool
     */
    public static function forwardDestination($source) {
        if (static::getType($source) !== static::$email_forward) {
            return false;
        }
        $result = \DB::connection("email")->table("virtual_aliases")->where('source', $source)->get();
        $return = [];
        foreach ($result as $row) {
            $return[] = $row->destination;
        }
        return $return;
    }
}

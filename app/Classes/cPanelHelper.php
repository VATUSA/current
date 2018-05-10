<?php
namespace App\Classes;

class cPanelHelper
{
    public static function buildConnect()
    {
        $xmlapi = new xmlapi("vatusa.net", env('CPANEL_USER', ''), env('CPANEL_PASS', ''));
        $xmlapi->password_auth(env('CPANEL_USER', ''), env('CPANEL_PASS', ''));
        $xmlapi->set_port(2083);
        $xmlapi->set_output('json');
        $xmlapi->set_debug(1);
        return $xmlapi;
    }

    public static function getPassStrength($pass)
    {
        $xmlapi = static::buildConnect();
        $res = $xmlapi->api2_query(env('CPANEL_USER',''), 'PasswdStrength', 'get_password_strength', ['password' => $pass]);
        $data = json_decode($res);
        return $data->cpanelresult->data[0]->strength;
    }

    public static function getType($email)
    {
        $user = explode("@", $email, 1);
        if (is_array($user)) {
            $user = $user[0];
        }

        $xmlapi = static::buildConnect();
        $res = $xmlapi->api2_query(env('CPANEL_USER', ''), 'Email', 'listforwards', ['domain' => 'vatusa.net', 'regex' => $user]);

        $res = json_decode($res);

        if (isset($res->cpanelresult->data[0]->forward)) return 1; // Forward

        return 0;   // Full
    }

    public static function getDest($email)
    {
        $user = explode("@", $email, 1);
        if (is_array($user)) {
            $user = $user[0];
        }

        $xmlapi = static::buildConnect();
        $res = $xmlapi->api2_query(env('CPANEL_USER', ''), 'Email', 'listforwards', ['domain' => 'vatusa.net', 'regex' => $user]);

        $res = json_decode($res);
        
        if (isset($res->cpanelresult->data[0]->forward))
            return $res->cpanelresult->data[0]->forward;

        return 0;
    }

    public static function emailCreate($email, $password)
    {
        $xmlapi = static::buildConnect();

        $p['domain'] = 'vatusa.net';
        $p['email'] = $email;
        $p['password'] = $password;
        $p['quota'] = "100";

        $res = $xmlapi->api2_query(env('CPANEL_USER', ''), 'Email', 'addpop', $p);
        if (preg_match("/error/", $res)) {
            return -1;
        }
        return 1;
    }

    public static function forwardCreate($email, $destination)
    {
        $xmlapi = static::buildConnect();

        $p['domain'] = 'vatusa.net';
        $p['email'] = $email;
        $p['fwdopt'] = 'fwd';
        $p['fwdemail'] = $destination;

        $res = $xmlapi->api2_query(env('CPANEL_USER', ''), 'Email', 'addforward', $p);
        if (preg_match("/error/", $res)) {
            return -1;
        }
        return 1;
    }

    public static function forwardDelete($email)
    {
        $xmlapi = static::buildConnect();

        $user = explode("@", $email);
        if (is_array($user)) {
            $user = $user[0];
        }

        $data = $xmlapi->api2_query(env('CPANEL_USER', ''), 'Email', 'listforwards', ['domain' => 'vatusa.net', 'regex' => $user]);
        $data = json_decode($data);
        foreach ($data->cpanelresult->data as $d) {
            static::deleteForward($user, $d->forward);
        }
    }

    public static function deleteForward($email, $destination)
    {
        $xmlapi = static::buildConnect();

        $fwds = explode(", ", $destination);
        foreach ($fwds as $fwd) {
            $p = array();
            $p['email'] = strtolower($email) . '@vatusa.net';
            $p['emaildest'] = $fwd;
            $res = $xmlapi->api2_query(env('CPANEL_USER', ''), 'Email', 'delforward', $p);
            if (preg_match("/error/", $res)) {
                print "ERROR $res<br><br><br>\n";
                return -1;
            }
        }
        $p = array();
        $p['email'] = strtolower($email) . '@vatusa.net';
        $p['emaildest'] = $destination;
        $res = $xmlapi->api2_query(env('CPANEL_USER', ''), 'Email', 'delforward', $p);
        return 1;
    }

    public static function emailDelete($email)
    {
        $xmlapi = static::buildConnect();

        $p['domain'] = 'vatusa.net';
        $p['email'] = strtolower($email);

        $res = $xmlapi->api2_query(env('CPANEL_USER', ''), 'Email', 'delpop', $p);
        if (preg_match("/error/", $res)) {
            return -1;
        }
        return 1;
    }

    public static function emailChangePassword($email, $password)
    {
        $xmlapi = static::buildConnect();

        $p['domain'] = 'vatusa.net';
        $p['email'] = strtolower($email) . "@vatusa.net";
        $p['password'] = $password;

        $res = $xmlapi->api2_query(env('CPANEL_USER', ''), 'Email', 'passwdpop', $p);
        if (preg_match("/error/", $res)) {
            return -1;
        }
        return 1;
    }
}
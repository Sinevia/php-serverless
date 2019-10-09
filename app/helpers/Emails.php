<?php

namespace App\Helpers;

class Emails
{


    public static $lastErrorMessage = "";

    public static function getAdminEmail()
    {
        return 'info@sinevia.com';
    }

    public static function getAppEmail()
    {
        return 'info@sinevia.com';
    }

    /**
     * Template for email address verification
     */
    function emailVerifyEmailAddress($user)
    {
        $token = \Sinevia\StringUtils::random('16', 'BCDFGHJKLMNPQRSTVWXZ');
        $appName = \Sinevia\Registry::get('APP_NAME', '{APP_NAME}');
        $isSuccessful = \Sinevia\Cache::createCache($token, $user['Id']);
        $link = \App\Helpers\Links::authEmailVerify(['Token' => $token]);

        $message = [];
        $message[] = 'Hi ' . $user['FirstName'] . ',';
        $message[] = '';
        $message[] = 'Please click the link bellow to activate your email address:';
        $message[] = '';
        $message[] = '<a href="' . $link . '">' . $link . '</a>';
        $message[] = '';
        $message[] = 'If you did not create an account, no further action is required';
        $message[] = '';
        $message[] = 'Regards,';
        $message[] = $appName;

        return $message;
    }

    public static function htmlMailAccountActivate($user)
    {
        $token = \Sinevia\StringUtils::random('16', 'BCDFGHJKLMNPQRSTVWXZ');
        $appName = \Sinevia\Registry::get('APP_NAME', '{APP_NAME}');
        $isSuccessful = \Sinevia\Cache::createCache($token, $user['Id']);
        $link = \App\Helpers\Links::authEmailVerify(['Token' => $token]);

        //$data = \Sinevia\DataUtils::serialize(['UserId' => $user->Id]);
        //$activation_link = \App\Helpers\Link::guestAccountActivate(['token' => $data]);
        $html_email = view('emails.auth.activate-account', array(
            'first_name' => $user['FirstName'],
            'activation_link' => $link,
        ));
        return $html_email;
    }

    public static function sendAccountActivationEmail($user, $subject = "")
    {
        $html_email = self::htmlMailAccountActivate($user);
        $text_email = \Sinevia\StringUtils::htmlEmailToText($html_email);
        $subject = $subject == "" ? \Sinevia\Registry::get('APP_NAME') . '. Account Activation Link' : $subject;

        $mailSent = self::sendMail(array(
            'From' => self::getAppEmail(),
            'To' => $user['Email'],
            'Cc' => self::getAdminEmail(),
            'Bcc' => '',
            //'Text' => $text_email,
            'Html' => $html_email,
            'Subject' => $subject,
        ));

        if ($mailSent == true) {
            return true;
        }
        return false;
    }

    public static function htmlPasswordChange($user)
    {
        $token = \Sinevia\StringUtils::random('16', 'BCDFGHJKLMNPQRSTVWXZ');
        $appName = \Sinevia\Registry::get('APP_NAME', '{APP_NAME}');
        $isSuccessful = \Sinevia\Cache::createCache($token, $user['Id']);
        $link = \App\Helpers\Links::authPasswordChange(['Token' => $token]);

        //$data = \Sinevia\DataUtils::serialize(['UserId' => $user->Id]);
        //$activation_link = \App\Helpers\Link::guestAccountActivate(['token' => $data]);
        $html_email = view('emails.auth-password-change', array(
            'first_name' => $user['FirstName'],
            'password_change_link' => $link,
        ));
        return $html_email;
    }

    public static function sendPasswordChangeEmail($user)
    {
        $html_email = self::htmlPasswordChange($user);
        $text_email = \Sinevia\StringUtils::htmlEmailToText($html_email);
        $subject = \Sinevia\Registry::get('APP_NAME') . '. Password Change Link';

        $mailSent = self::sendMail(array(
            'From' => self::getAppEmail(),
            'To' => $user['Email'],
            'Cc' => self::getAdminEmail(),
            'Bcc' => '',
            //'Text' => $text_email,
            'Html' => $html_email,
            'Subject' => $subject,
        ));

        if ($mailSent == true) {
            return true;
        }
        return false;
    }


    public static function sendMailgunMail($to, $from, $subject, $html, $text, $cc, $bcc)
    {
        $mg = new \Mailgun\Mailgun(\Config::mailgunKey());
        $domain = \Config::mailgunDomain();
        $message = [];
        $message['from'] = $from;
        $message['to'] = $to;
        $message['subject'] = $subject;
        $message['text'] = $text;
        $message['html'] = $html;
        if ($cc != "") {
            $message['cc'] = $cc;
        }
        if ($bcc != "") {
            $message['bcc'] = $bcc;
        }

        $result = $mg->sendMessage($domain, $message);

        return true;
    }

    public static function sendSpakpostMail($to, $from, $subject, $html, $text, $cc, $bcc)
    {
        self::$lastErrorMessage = "";
        $fromArray = self::parseAddressList($from);
        $toArray = self::parseAddressList($to);
        $ccArray = self::parseAddressList($cc);
        $bccArray = self::parseAddressList($bcc);

        $transmissionData = [
            'content' => [
                'from' => [
                    'name' => $from,
                    'email' => 'postmaster@test.com',
                ],
                'subject' => $subject,
                'text' => $text,
                'html' => $html,
            ],
            'recipients' => [],
            //'cc' => [],
            //'bcc' => [],
        ];

        foreach ($fromArray as $email => $name) {
            $transmissionData['content']['from'] = [
                'name' => $name,
                'email' => 'postmaster@test.com',
            ];
            break; // Only first one
        }

        foreach ($toArray as $email => $name) {
            $transmissionData['recipients'][] = ['address' => ['name' => $name, 'email' => $email]];
        }

        if (count($ccArray) > 0) {
            $transmissionData['cc'] = [];
            foreach ($ccArray as $email => $name) {
                $transmissionData['cc'][] = ['address' => ['name' => $name, 'email' => $email]];
            }
        }

        if (count($bccArray) > 0) {
            $transmissionData['bcc'] = [];
            foreach ($bccArray as $email => $name) {
                $transmissionData['bcc'][] = ['address' => ['name' => $name, 'email' => $email]];
            }
        }


        $httpClient = new \Http\Adapter\Guzzle6\Client(new \GuzzleHttp\Client());
        $sparkPost = new \SparkPost\SparkPost($httpClient, ['key' => '']);

        try {
            $sparkPost->setOptions(['async' => false]);
            $response = $sparkPost->transmissions->post($transmissionData);
            $statusCode = $response->getStatusCode();
            $result = $response->getBody();
            //var_dump($statusCode);
            //var_dump($result);
            if ($statusCode == 200) {
                return true;
            }
            self::$lastErrorMessage = json_encode(['code' => $statusCode, 'result' => $result]);
            return false;
        } catch (\Exception $e) {
            self::$lastErrorMessage = json_encode(['code' => $e->getCode(), 'result' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Returns an array
     * [
     *     'Email1'=>'Name1'
     *     'Email2'=>'Name2'
     * ]
     * @param type $addressString
     * @return type
     */
    public static function parseAddressList($addressString)
    {
        $addressArray = array_filter(str_getcsv($addressString, ",", '"'));
        $emailList = array();

        foreach ($addressArray as $addressString) {
            $addressString = trim($addressString);
            /* Normal email? Turn to name <email> */
            if (strpos($addressString, '<') === false) {
                $addressString = $addressString . ' <' . $addressString . '>';
            }

            preg_match('~(?:([^<]*?)\s*)?<(.*)>~', $addressString, $var);

            $email = $var[1];
            $name = $var[2];

            $emailList[$email] = $name;
        }

        return $emailList;
    }


    /**
     * Sends an email through the Sinevia API
     * <code>
     * Application::sendMail(array(
     *      'From' => 'test@test.com',
     *      'To' => 'test1@test.com',
     *      'Cc' => 'test2@test.com',
     *      'Bcc' => 'test3@test.com',
     *      'Text' => 'Here is the text part',
     *      'Html' => '<p>Here is the HTML part</p>',
     *      'Subject' => 'This is a test email at ' . date('Y-m-d H:i:s'),
     * ));
     * </code>
     * @return boolean
     */
    public static function sendMail($parameters, $debug = false)
    {
        $http = new \Sinevia\HttpClient();
        $http->setUrl('http://ws.sinevia.com/mails/mail-send');
        $http->post(array_merge(array('Token' => 'YOURTOKEN'), $parameters));

        $response_status = $http->getResponseStatus();
        $response_body = $http->getResponseBody();
        if ($debug == true) {
            var_dump($response_status);
            var_dump($response_body);
        }

        if ($response_status != '200') {
            return false;
        }

        $pos = strpos($response_body, 'success');
        if ($pos !== false) {
            return true;
        }

        return false;
    }
}
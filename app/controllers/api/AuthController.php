<?php

namespace App\Controllers\Api;

class AuthController extends BaseController
{

    public function anyIndex()
    {
        return $this->success('Api is working', ['time' => date('Y-m-d H:i:s')]);
    }

    function anyEmailVerify()
    {
        $token = req('token');

        if ($token == '') {
            return $this->error('Verification failed. Token is required');
        }

        $userId = \Sinevia\Cache::findCacheByKey($token);

        if ($userId == null) {
            return $this->error('Verification failed. Token has expired');
        }

        $user = \App\Plugins\UserPlugin::getUserById($userId);

        if ($user == \null) {
            return $this->error('Verification failed. User could not be found.');
        }

        $isPendingVerification = ($user['Status'] == \App\Plugins\UserPlugin::STATUS_PENDING_VERIFICATION);

        if ($isPendingVerification == false) {
            return $this->success('Your account has already been verified. You may login now.');
        }

        $isOk = \App\Plugins\UserPlugin::updateUserById($userId, ['Status' => \App\Plugins\UserPlugin::STATUS_ACTIVE]);

        if ($isOk) {
            return $this->success('Your account was successfully verified. You may login now.');
        }

        return $this->error('Verification failed. There was an IO error. Please try again later');
    }

    public function anyLogin()
    {
        $email = trim(\req('Email', ''));
        $password = trim(\req('Password', ''));

        if ($email == '') {
            return $this->error('Email not specified');
        }
        if ($password == '') {
            return $this->error('Password not specified');
        }

        //\App\Plugins\Users::getDatabase()->debug = true;
        $user = \App\Plugins\UserPlugin::getUserByEmail($email);

        if ($user == null) {
            return $this->error('User not found');
        }

        if ($user['Status'] == \App\Plugins\UserPlugin::STATUS_PENDING_VERIFICATION) {
            \App\Helpers\Emails::sendAccountActivationEmail($user);
            return $this->error('Your account is pending activation. <br /> Check your mailbox. <br /> We sent you a new activation email.');
        }

        if (\password_verify($password, $user['Password'])) {
            unset($user['Password']);
            return $this->success('Authentication successful', ['user' => $user, 'token' => $this->userTokenCreate($user)]);
        }

        if (password_verify($password, $user['Password']) == false) {
            return $this->error('Authentication failed');
        }

        unset($user['Password']);
        return $this->success('Authentication successful', [
            'user' => $user,
            'token' => $this->userTokenCreate($user)
        ]);
    }

    /**
     * Proceses the password restore page
     */
    function anyPasswordChange()
    {
        $token = req('Token');
        $password = trim(req('Password'));

        if ($token == '') {
            return $this->error('Verification failed. Token is required');
        }

        if ($password == '') {
            return $this->error('Password is required field');
        }

        if (\Sinevia\StringUtils::hasMinumumChars($password, 10) == false) {
            return $this->error('Password must have at least 10 characters');
        }

        $userId = \Sinevia\Cache::findCacheByKey($token);

        if ($userId == null) {
            return $this->error('Verification failed. Token has expired');
        }

        $user = \App\Plugins\UserPlugin::getUserById($userId);

        if ($user == \null) {
            return $this->error('Verification failed. User could not be found.');
        }

        $password_hash = \password_hash($password, PASSWORD_DEFAULT);

        //\App\Plugins\UserPlugin::getDatabase()->debug = true;
        $isSuccessful = \App\Plugins\UserPlugin::updateUserById($user['Id'], array(
            'Password' => $password_hash
        ));

        if ($isSuccessful == false) {
            return $this->error('Changing password failed');
        }

        return $this->success('Password was successfully changed. You may login now');
    }

    /**
     * Proceses the password restore page
     */
    public function anyPasswordRestore()
    {
        $email = req('Email', '');
        $firstName = req('FirstName', '');
        $lastName = req('LastName', '');

        if ($email == '') {
            return $this->error('Email is required');
        }

        if ($firstName == '') {
            return $this->error('FirstName is required');
        }

        if ($lastName == '') {
            return $this->error('LastName is required');
        }

        $user = \App\Plugins\UserPlugin::getUserByEmail($email);

        if ($user == null) {
            return $this->error('User not found');
        }

        if (strtolower($user['FirstName']) !== strtolower($firstName)) {
            return $this->error('Authentication failed');
        }

        if (strtolower($user['LastName']) !== strtolower($lastName)) {
            return $this->error('Authentication failed');
        }

        $isMailSent = \App\Helpers\Emails::sendPasswordChangeEmail($user);

        if ($isMailSent == true) {
            //session_regenerate_id();
            $message = 'Password change instructions have been sent to you via email.';
            return $this->success($message);
        } else {
            $message = 'There was a system problem with sending emails. Please try again later';
            return $this->error($message);
        }

        $password = \Sinevia\StringUtils::random(12, 'BCDFGHKLMNPRSTWXZ');
        $password_hash = \password_hash($password, PASSWORD_DEFAULT);

        // \App\Plugins\Users::getDatabase()->debug = true;
        $isSuccessful = \App\Plugins\Users::updateUserById($user['Id'], array(
            'PasswordHash' => $password_hash
        ));

        if ($isSuccessful == false) {
            return $this->error('Restoring password failed');
        }

        $message[] = 'New password:' . $password;

        $html_email = implode("<br />\n", $message);
        $text_email = implode("\n", $message);

        $mail_sent = \App\Helpers\Emails::sendMail(array(
            'From' => \App\Helpers\Emails::getAdminEmail(),
            'To' => $email,
            'Cc' => \App\Helpers\Emails::getAdminEmail(),
            'Bcc' => '',
            'Text' => $text_email,
            'Html' => $html_email,
            'Subject' => 'Password Change Instructions',
        ));

        if ($mail_sent == true) {
            //session_regenerate_id();
            $message = 'New Password has been sent to you via email.';
            return $this->success($message);
        } else {
            $message = 'There was a system problem with sending emails. Please try again later';
            return $this->error($message);
        }
    }

    public function anyRegister()
    {
        $firstName = trim(req('FirstName', ''));
        $lastName = trim(req('LastName', ''));
        $email = trim(req('Email', ''));
        $password = trim(req('Password'));

        if ($email == '') {
            return $this->error('Email is required field');
        }

        if ($firstName == '') {
            return $this->error('First Name is required field');
        }

        if ($lastName == '') {
            return $this->error('Last Name is required field');
        }

        if ($email == '') {
            return $this->error('E-mail not specified');
        }
        if ($password == '') {
            return $this->error('Password not specified');
        }
        if (\Sinevia\StringUtils::isEmail($email) == false) {
            return $this->error('E-mail is not valid');
        }
        if (\Sinevia\StringUtils::hasMinumumChars($password, 10) == false) {
            return $this->error('Password must have at least 10 characters');
        }

        //\App\Plugins\Users::getDatabase()->debug = true;
        $user = \App\Plugins\UserPlugin::getUserByEmail($email);

        if ($user != null) {
            return $this->error('User with this email already exists');
        }

        // DEBUG: \db()->debug = true;
        $user = \App\Plugins\UserPlugin::createUser([
            'Status' => \App\Plugins\UserPlugin::STATUS_PENDING_VERIFICATION,
            'FirstName' => $firstName,
            'LastName' => $lastName,
            'Email' => $email,
            'Password' => \password_hash($password, \PASSWORD_DEFAULT),
        ]);

        if ($user == null) {
            return $this->error('User failed to be created');
        }

        $isOk = \App\Helpers\Emails::sendAccountActivationEmail($user);

        if ($isOk == true) {
            //session_regenerate_id();
            $message = 'Confirmation email has been sent to your email address. Please read it to confirm your account.';
            return $this->success($message);
        } else {
            $message = 'There was a system problem with sending emails. Please try again later';
            return $this->error($message);
        }
    }
}
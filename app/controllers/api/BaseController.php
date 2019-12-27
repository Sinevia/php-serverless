<?php

namespace App\Controllers\Api;

class BaseController {

    private $tokenPassword = 'PAnK>!suB}k:l/>WBh-@AlNrz;1B/).u,xx[}H+t^SuQt0sJ*7[tXlw?SIZC)75';
    public $userId = null;

    public function verifyUserRequest() {
        $token = req('Token');
        $userId = $this->userIdFromToken($token);
        if (is_null($userId)) {
            return $this->authenticationFailed('Your session has expired');
        }
        $this->userId = $userId;
    }

    /**
     * 
     * @param UserEntity $userEntity
     */
    protected function userTokenCreate($user) {
        $userId = $user['Id'];
        $ip = \Sinevia\Utils::ip();
        $expires = time() + (1 * 60 * 60);
        $data = array('userId' => $userId, 'ip' => $ip, 'expires' => $expires);
        //$data2 = base_convert(time(), 10, 36) . '$' . $userId . '$' . $ip;
        //list($expires, $userId, $ip) = explode('$', $data2);
        $token = \Sinevia\DataUtils::serialize($data, $this->tokenPassword);
        return $token;
    }

    /**
     * Returns the UserId if successful, false otherwise
     * @param string $token
     * @return string|null
     */
    protected function userIdFromToken($token) {
        if ($token == '') {
            return null;
        }

        $data = @\Sinevia\DataUtils::unserialize($token, $this->tokenPassword);

        if (is_array($data) == false) {
            return null;
        }

        $userId = $data['userId'];
        $ip = $data['ip'];
        $expires = $data['expires'];

        if ($ip !== \Sinevia\Utils::ip()) {
            return null;
        }

        if (time() > $expires) {
            return null;
        }

        return $userId;
    }

    /**
     * @param mixed $msg
     * @return string
     */
    protected function error($message = null) {
        $response = new \Sinevia\ApiResponse;
        $response->setStatus(\Sinevia\ApiResponse::ERROR);
        if ($message != null) {
            $response->setMessage($message);
        }
        $json = $response->toJson();
        return isset($_REQUEST['callback']) ? "{$_REQUEST['callback']}($json)" : $json;
    }

    /**
     * @param mixed $msg
     * @return string
     */
    protected function success($message, $data = null) {
        $response = new \Sinevia\ApiResponse;
        $response->setStatus(\Sinevia\ApiResponse::SUCCESS);
        $response->setMessage($message);
        if ($data != null) {
            $response->setData($data);
        }
        $json = $response->toJson();
        return isset($_REQUEST['callback']) ? "{$_REQUEST['callback']}($json)" : $json;
    }

    /**
     * @param mixed $msg
     * @return string
     */
    protected function authenticationFailed($statusMessage = 'Authentication failed', $data = null) {
        $response = new \Sinevia\ApiResponse;
        $response->setStatus(\Sinevia\ApiResponse::AUTHENTICATION_FAILED);
        if ($statusMessage != null) {
            $response->setMessage($statusMessage);
        }
        if ($data != null) {
            $response->setData($data);
        }
        $json = $response->toJson();
        return isset($_REQUEST['callback']) ? "{$_REQUEST['callback']}($json)" : $json;
    }

    protected function isDebug() {
        $debug = ($_REQUEST['debug'] ?? 'no');
        return ($debug == 'yes') ? true : false;
    }

}

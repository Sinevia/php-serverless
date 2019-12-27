<?php

namespace App\Controllers\Guest;

class BaseController {
    /**
     * Shows a flashing message
     * @param string $message
     * @param string $url
     * @param int $time
     * @return string
     */    
    function flash() {
        $info = once('info', '');
        $error = once('error', '');
        $success = once('success', '');
        $warning = once('warning', '');
        $url = once('url', '');
        $time = once('time', '');
        $webpage_title = 'System Message';
        $webpage_content = ui('guest/flash.phtml', get_defined_vars());
        return ui('guest/layout.phtml', get_defined_vars());
    }

    /**
     * Shows a flashing message
     * @param string $message
     * @param string $url
     * @param int $time
     * @return string
     */
    protected function flashError($message, $url = '', $time = 10) {
        $_SESSION['error'] = $message;
        $_SESSION['time'] = $time;
        $_SESSION['url'] = $url;
        return $this->flash();
    }

    /**
     * Shows a flashing message
     * @param string $message
     * @param string $url
     * @param int $time
     * @return string
     */
    protected function flashSuccess($message, $url = '', $time = 10) {
        $_SESSION['success'] = $message;
        $_SESSION['time'] = $time;
        $_SESSION['url'] = $url;
        return $this->flash();
    }

    protected function flashInfo($message, $url = '', $time = 10) {
        $_SESSION['info'] = $message;
        $_SESSION['time'] = $time;
        $_SESSION['url'] = $url;
        return $this->flash();
    }

    protected function flashWarning($message, $url = '', $time = 10) {
        $_SESSION['warning'] = $message;
        $_SESSION['time'] = $time;
        $_SESSION['url'] = $url;
        return $this->flash();
    }
}
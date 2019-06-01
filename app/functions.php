<?php

/**
 * Returns the top most (root, base) path of the application
 * @return string
 */
function basePath($path = '') {
    $rootPath = dirname(__DIR__);
    if ($path == "") {
        return $rootPath;
    }
    return $rootPath . '/' . ltrim($path, '/');
}

/**
 * Returns the top most (root, base) URL of the application
 * @return string
 */
function baseUrl($path = '') {
    $url = \Sinevia\Registry::get("URL_BASE");
    return $url . '/' . ltrim($path, '/');
}

/**
 * Converts an image path to data URI
 * @return string
 */
function image2DataUri($imagePath) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($imagePath);
    return 'data:' . $type . ';base64,' . base64_encode(file_get_contents($imagePath));
}

/**
 * Checks if this is a GET request
 * @return boolean
 */
function isGet() {
    $isPost = strtolower($_SERVER['REQUEST_METHOD']) == "get" ? true : false;
    return $isPost;
}

/**
 * Checks if this is a POST request
 * @return boolean
 */
function isPost() {
    $isPost = strtolower($_SERVER['REQUEST_METHOD']) == "post" ? true : false;
    return $isPost;
}

/**
 * Joins multiple CSS files, and optionally minifies them
 * @return string
 */
function joinCss($styles, $options = []) {
    $minify = $options['minify'] ?? 'no';
    $html = '';
    $html .= '<style>';
    foreach ($styles as $style) {
        $path = basePath('public/' . trim($style, '/'));
        // DEBUG: $html .= '/* '.$path.' */';
        if (file_exists($path)) {
            $contents = file_get_contents($path);
            //if ($minify == "yes") {
            //    $contents = ';' . \JSMin\JSMin::minify($contents);
            //}
            $html .= $contents;
        }
    }
    $html .= '</style>';
    return $html;
}

/**
 * Joins multiple JavaScript files, and optionally minifies them
 * @return string
 */
function joinScripts($scripts, $options = []) {
    $minify = $options['minify'] ?? 'no';
    $html = '';
    $html .= '<script>';
    foreach ($scripts as $script) {
        $path = basePath('public/' . trim($script, '/'));
        if (file_exists($path)) {
            $contents = file_get_contents($path);
            if ($minify == "yes") {
                $contents = ';' . \JSMin\JSMin::minify($contents);
            }
            $html .= $contents;
        }
    }
    $html .= '</script>';
    return $html;
}


/**
 * Redirects to the specified URL
 * @return string
 */
function redirect($url) {
    return "<meta http-equiv=\"refresh\" content=\"0;url=$url\">\r\n";
}


/**
 * Returns the requested name-value pair if it exists
 * @return mixed
 */
function req($name, $default = null, $functions = []) {
    $value = (isset($_REQUEST[$name]) == false) ? $default : $_REQUEST[$name];
    foreach ($functions as $fn) {
        $value = $fn($value);
    }
    return $value;
}

<?php

namespace Flix\FW\Http;

class Request {

    public static function back() {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        die();
    }

    public static function redirect($url, $statusCode = 303) {
        header('Location: ' . $url, true, $statusCode);
        die();
    }

}
<?php

namespace ASPEN;

class Config {
    private static $data;

    public static function getOriginInformation() {
        $origin             = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        $requestMethod      = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        $acRequestMethod    = isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) ? $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] : '';
        $acRequestHeaders   = isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']) ? $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] : '';

        return ['origin'        => $origin,
            'requestMethod'     => $requestMethod,
            'acRequestMethod'   => $acRequestMethod,
            'acRequestHeaders'  => $acRequestHeaders];
    }

    public static function checkOrigin(array $info = []) {
        header('Access-Control-Allow-Origin: '.$info['origin']);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');

        if ($info['requestMethod'] == 'OPTIONS') {
            if ($info['acRequestMethod'] != '') header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            if ($info['acRequestHeaders'] != '') header('Access-Control-Allow-Headers: '.$info['acRequestHeaders']);
            exit(0);
        }
    }

    public static function load($file) {
        $contents = file_get_contents($file);
        if (!$contents) {
            throw new Exception('Failed to load configuration because the file cannot be found or opened.');
        }

        self::$data = json_decode($contents, true);
        return true;
    }

    public static function loadKeys($privateFile, $publicFile) {
        $private = file_get_contents($privateFile);
        if (!$private) {
            throw new Exception('Failed to load the private key file because it couldn\'t be found or opened.');
        }

        $public = file_get_contents($publicFile);
        if (!$public) {
            throw new Exception('Failed to load the public key file because it couldn\'t be found or opened.');
        }

        static::$data['keys'] = [ 'private' => $private, 'public' => $public ];
    }

    public static function get($key) {
        return self::$data[$key];
    }
}

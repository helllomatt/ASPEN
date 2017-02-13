<?php

namespace ASPEN;

class Config {
    private static $data;

    public static function load($file) {
        $contents = file_get_contents($file);
        if (!$contents) {
            throw new Exception("Failed to load configuration because the file cannot be found or opened.");
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

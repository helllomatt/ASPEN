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

    public static function get($key) {
        return self::$data[$key];
    }
}

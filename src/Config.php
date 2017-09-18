<?php

namespace ASPEN;

use Exception;

class Config {
    private static $data;

    /**
     * Gets the origin information for the request, this is for CORS
     *
     * @return array
     */
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

    /**
     * Checks the origin to see if this is a valid method from a valid source, this is for CORS
     *
     * @param  array  $info
     * @return void
     */
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

    /**
     * Loads a config file into a static variable to be used throughout the request
     *
     * @param  string  $file
     * @return boolean
     */
    public static function load($file) {
        if (!file_exists($file)) {
            throw new Exception('Failed to load configuration because the file cannot be found or opened.');
        }

        $contents = file_get_contents($file);

        self::$data = json_decode($contents, true);
        return true;
    }

    /**
     * Loads private and public keys from the config file and saves their information
     * in a static variable to be used across the request, this was for tokens
     *
     * @param  string  $privateFile
     * @param  string  $publicFile
     * @return void
     */
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

    /**
     * Gets a value from the config file
     *
     * @param  string  $key
     * @return any
     */
    public static function get($key, $return_null = false) {
        if (!array_key_exists($key, self::$data) && $return_null) return null;
        return self::$data[$key];
    }

    /**
     * Gets database config information from the config file
     *
     * @param  string $db
     * @return array
     */
    public static function getDBConfig($db) {
        if (!self::$data) throw new Exception('Failed to get database configuration, no configuration file was loaded.');
        if (array_key_exists('databases', self::$data)) {
            if (array_key_exists($db, self::$data['databases'])) {
                return self::$data['databases'][$db];
            } else throw new Exception('Failed to get database configuration, none was provided.');
        } else throw new Exception('Failed to get database configuration, no databases are given.');
    }

    /**
     * Adds a custom variable to be used throughout the request in the static
     * variable
     *
     * @param string  $key
     * @param any $value
     */
    public static function add($key, $value) {
        static::$data[$key] = $value;
    }
}

<?php

namespace ASPEN;

use ASPEN\Response;
use Exception;

class Connector {
    private $data;
    private $databases = [];

    private $transfer_data = [];

    /**
     * Allows data to be transferred from a prerun to the endpoint
     *
     * @param  string $key
     * @param  any  $value
     * @return void
     */
    public function transfer($key, $value) {
        $this->transfer_data[$key] = $value;
    }

    /**
     * Tries to get data that should've come from a prerun
     *
     * @param  string $key
     * @return any|NULL
     */
    public function getTransferred($key) {
        if (!array_key_exists($key, $this->transfer_data)) return null;
        else return $this->transfer_data[$key];
    }

    /**
     * Sets data (variables from the request) to the connector to be got by the
     * endpoints
     *
     * @param any $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * Returns a variable if it has been set by the incoming request
     *
     * @param  string  $key
     * @return any|NULL
     */
    public function getVariable($key) {
        if (array_key_exists($key, $this->data)) return $this->data[$key];
        else return null;
    }

    /**
     * Checks the method used to make the request
     *
     * @param  string  $method
     * @return boolean
     */
    public function usingMethod($method) {
        return $_SERVER['REQUEST_METHOD'] == strtoupper($method);
    }

    /**
     * Gets a database handle if none exists based on the information pulled from
     * the config file.
     *
     * @param  string $db
     * @return Double\DB;
     */
    public function getDB($db) {
        if (array_key_exists($db, $this->databases)) return $this->databases[$db];
        elseif (!class_exists('Double\DB')) {
            throw new Exception('Database library not loaded.');
        } else {
            $config = Config::getDBConfig($db);
            $this->databases[$db] = (new \Double\DB())
                ->connect($config['host'], $config['username'], $config['password'], $config['db']);
            return $this->databases[$db];
        }
    }
}

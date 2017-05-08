<?php

namespace ASPEN;

use ASPEN\Response;
use Exception;

class Connector {
    private $data;
    private $databases = [];

    public function setData($data) {
        $this->data = $data;
    }

    public function getVariable($key) {
        if (array_key_exists($key, $this->data)) return $this->data[$key];
        else return null;
    }

    public function usingMethod($method) {
        return $_SERVER['REQUEST_METHOD'] == strtoupper($method);
    }

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

<?php

namespace ASPEN;

use ASPEN\Database\DB;
use ASPEN\Response;

class Connector {
    private $data;
    private $database = null;

    public function setData($data) {
        $this->data = $data;
    }

    public function getVariable($key) {
        if (array_key_exists($key, $this->data)) return $this->data[$key];
        else return null;
    }

    public function setDatabase(DB $db = null) {
        $this->database = $db;
    }

    public function db() {
        return $this->database;
    }

    public function usingMethod($method) {
        return $_SERVER['REQUEST_METHOD'] == strtoupper($method);
    }
}

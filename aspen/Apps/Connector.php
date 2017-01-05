<?php

namespace ASPEN;

use ASPEN\Database\DB;

class Connector {
    private $data;
    private $database;

    public function setData($data) {
        $this->data = $data;
    }

    public function getVariable($key) {
        if (array_key_exists($key, $this->data)) return $this->data[$key];
        else return null;
    }

    public function setDatabase(DB $db) {
        $this->database = $db;
    }

    public function db() {
        return $this->database;
    }
}

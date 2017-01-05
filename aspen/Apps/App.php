<?php

namespace ASPEN;

use ASPEN\Database\DB;

class App {
    private $number;
    private $router;
    private $name;
    private $database;
    private $callbacks = [];

    public function __construct($name = '') {
        $this->router = new Router();
        $this->name = $name;
    }

    public function version($number) {
        $this->version = $number;
    }

    public function get($route, $callback) {
        if ($this->router->matches('v'.$this->version.'/'.$route)) {
            $this->callbacks[] = $callback;
        }
    }

    public function getName() {
        return $this->name;
    }

    public function setDatabase(DB $db) {
        $this->database = $db;
    }

    public function run() {
        if (empty($this->callbacks)) return false;
        $connector = new Connector();
        $connector->setData($this->router->getVariables());
        $connector->setDatabase($this->database);
        for ($i = 0; $i < count($this->callbacks); $i++) {
            $this->callbacks[$i]($connector);
        }

        return true;
    }
}

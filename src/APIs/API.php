<?php

namespace ASPEN;

use ASPEN\Database\DB;

class API {
    private $number;
    private $router;
    private $name;
    private $database = null;
    private $callbacks = [];

    public function __construct($name = '') {
        $this->router = new Router();
        $this->name = $name;
    }

    public function version($number) {
        $this->version = $number;
    }

    public function add(Endpoint $ep) {
        if ($this->router->matches('v'.$this->version.'/'.$ep->route)) {
            $this->callbacks[] = $ep;
        }
    }

    public function get($route, $callback) {
        if ($this->router->matches('v'.$this->version.'/'.$route)) {
            $this->callbacks[] = $callback;
        }
    }

    public function getName() {
        return $this->name;
    }

    public function setDatabase(DB $db = null) {
        $this->database = $db;
    }

    public function run() {
        if (empty($this->callbacks)) return false;
        $connector = new Connector();
        $connector->setData($this->router->getVariables());
        $connector->setDatabase($this->database);

        $good = [];
        for ($i = 0; $i < count($this->callbacks); $i++) {
            if (is_a($this->callbacks[$i], 'ASPEN\Endpoint')) {
                if ($this->callbacks[$i]->runCallback($connector) !== false) $good[] = true;
            } elseif ($this->callbacks[$i]($connector) !== false) $good[] = true;
        }

        return $good;
    }
}

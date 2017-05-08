<?php

namespace ASPEN;

use ASPEN\Database\DB;

class API {
    private $number;
    private $router;
    private $name;
    private $database = null;
    private $callbacks = [];

    public function __construct($name = '', Router $router = null) {
        $this->router = $router ? $router : new Router(filter_input(INPUT_GET, 'route'));
        $this->name = $name;
        return $this;
    }

    public function version($number) {
        $this->version = $number;
        return $this;
    }

    public function getEndpoints() {
        return $this->callbacks;
    }

    public function add(Endpoint $ep) {
        if ($this->router->matches('v'.$this->version.'/'.$ep->route)) {
            $this->callbacks[] = $ep;
        }

        return $this;
    }

    public function get($route, $callback) {
        if ($this->router->matches('v'.$this->version.'/'.$route)) {
            $this->callbacks[] = $callback;
        }

        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function getVersion() {
        return $this->version;
    }

    public function run(Connector $c = null) {
        if (empty($this->callbacks)) return false;
        $connector = $c ? $c : new Connector();
        $connector->setData($this->router->getVariables());

        $good = [];
        for ($i = 0; $i < count($this->callbacks); $i++) {
            if (is_a($this->callbacks[$i], 'ASPEN\Endpoint')) {
                if ($this->callbacks[$i]->attachConnector($connector)->runCallback() !== false) $good[] = true;
            } elseif ($this->callbacks[$i]($connector) !== false) $good[] = true;
        }

        return $good;
    }
}

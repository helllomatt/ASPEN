<?php

namespace ASPEN;

use ASPEN\Database\DB;
use Exception;

class API {
    private $number;
    private $router;
    private $name;
    private $database = null;
    private $callbacks = [];
    private $preruns = [];

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

    public function addPreRun($name, $function) {
        $this->preruns[$name] = $function;
    }

    public function getPreRun($name) {
        if (!array_key_exists($name, $this->preruns)) return null;
        else return $this->preruns[$name];
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
            $endpointPreRuns = $this->callbacks[$i]->getPreRuns();
            if (!empty($endpointPreRuns)) {
                foreach ($endpointPreRuns as $name) {
                    if (array_key_exists($name, $this->preruns)) {
                        try {
                            $run = $this->preruns[$name](new Response(), $connector);
                            if ($run === false) $good[] = false;
                            elseif ($run === null || $run === true) $good[] = true;
                        } catch (Exception $e) { $good[] = false; }
                    } else {
                        throw new Exception("Prerun '".$name."' doesn't exist");
                    }
                }
            }

            if (!in_array(false, $good)) {
                if (is_a($this->callbacks[$i], 'ASPEN\Endpoint')) {
                    if ($this->callbacks[$i]->attachConnector($connector)->runCallback() !== false) $good[] = true;
                } elseif ($this->callbacks[$i]($connector) !== false) $good[] = true;
            }
        }

        return $good;
    }
}

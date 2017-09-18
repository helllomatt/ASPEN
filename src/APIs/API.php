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

    /**
     * Constructs the class, connecting to the router
     *
     * @param string $name
     * @param ASPEN\API $router
     */
    public function __construct($name = '', Router $router = null) {
        $this->router = $router ? $router : new Router(filter_input(INPUT_GET, 'route'));
        $this->name = $name;
        return $this;
    }

    /**
     * Defines the version of the API
     *
     * @param  number $number
     * @return ASPEN\API
     */
    public function version($number) {
        $this->version = $number;
        return $this;
    }

    /**
     * Returns all of the registered endpoints
     *
     * @return array
     */
    public function getEndpoints() {
        return $this->callbacks;
    }

    /**
     * Registers an endpoint to the request
     *
     * @param ASPEN\Endpoint $ep
     * @return ASPEN\API
     */
    public function add(Endpoint $ep) {
        if ($this->router->matches('v'.$this->version.'/'.$ep->route)) {
            $this->callbacks[] = $ep;
        }

        return $this;
    }

    /**
     * Checks to see if the request matches the route url, then adds the
     * callback to be run once everything has been figured out
     *
     * @param  string $route
     * @param  function  $callback
     * @return ASPEN\API
     */
    public function get($route, $callback) {
        if ($this->router->matches('v'.$this->version.'/'.$route)) {
            $this->callbacks[] = $callback;
        }

        return $this;
    }

    /**
     * Registers a prerun function to the api
     *
     * @param string  $name
     * @param function  $function
     */
    public function addPreRun($name, $function) {
        $this->preruns[$name] = $function;
    }

    /**
     * Gets a prerun callback by name
     *
     * @param  string $name
     * @return function
     */
    public function getPreRun($name) {
        if (!array_key_exists($name, $this->preruns)) return null;
        else return $this->preruns[$name];
    }

    /**
     * Gets the name of the API
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the version of the API
     *
     * @return number
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Runs the callbacks that match the endpoints in this API
     *
     * @param  ASPEN\Connector  $c
     * @return array
     */
    public function run(Connector $c = null) {
        if (empty($this->callbacks)) return false;
        $connector = $c ? $c : new Connector();
        $connector->setData($this->router->getVariables());

        $good = [];

        for ($i = 0; $i < count($this->callbacks); $i++) {
            // run the pre runs
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

            // run the endpoint callbacks if the preruns didn't fail out
            if (!in_array(false, $good)) {
                if (is_a($this->callbacks[$i], 'ASPEN\Endpoint')) {
                    if ($this->callbacks[$i]->attachConnector($connector)->runCallback() !== false) $good[] = true;
                } elseif ($this->callbacks[$i]($connector) !== false) $good[] = true;
            }
        }

        return $good;
    }
}

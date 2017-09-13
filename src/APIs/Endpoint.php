<?php

namespace ASPEN;

use Exception;

class Endpoint {
    public $route;
    public $method;
    public $preruns = [];
    private $callback;

    private $connector;
    private $response;

    /**
     * Defines data about the endpoint
     *
     * @param array $info
     */
    public function __construct($info = []) {
        if (!array_key_exists('to', $info)) throw new Exception('Invalid route given to endpoint.');
        $this->route  = $info['to'];

        if (array_key_exists('method', $info)) $this->method = $info['method'];
        if (array_key_exists('preruns', $info)) $this->preruns = $info['preruns'];
        return $this;
    }

    /**
     * Defines the callback once the object has been initialized with data
     *
     * @param  function  $callback
     * @return ASPEN\Endpoint
     */
    public function then($callback) {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Returns the defined callback
     *
     * @return function
     */
    public function getCallback() {
        return $this->callback;
    }

    /**
     * Returns the names of the preruns that should be ran
     *
     * @return array
     */
    public function getPreRuns() {
        return $this->preruns;
    }

    /**
     * Attaches a connector to this endpoint for data grabbing
     *
     * @param  Connector $c
     * @return ASPEN\Endpoint
     */
    public function attachConnector(Connector $c) {
        $this->connector = $c;
        return $this;
    }

    /**
     * Returns the connector that has been attached to this object
     *
     * @return ASPEN\Connector
     */
    public function getConnector() {
        return $this->connector;
    }

    /**
     * Attaches a response to this endpoint
     *
     * @param  Response $r
     * @return ASPEN\Connector
     */
    public function attachResponse(Response $r) {
        $this->response = $r;
        return $this;
    }

    /**
     * Returns the response attached to this endpoint, or creates one if one hasn't already.
     *
     * @return ASPEN\Response
     */
    public function getResponse() {
        return $this->response == null ? new Response() : $this->response;
    }

    /**
     * Runs the callback if the endpoint method has been defined and matches
     *
     * @return boolean
     */
    public function runCallback() {
        $cb = $this->callback;
        $connector = $this->getConnector();

        if ($this->method) {
            if (!$connector->usingMethod($this->method)) return false;
        }

        return $cb($this->getResponse(), $connector);
    }
}

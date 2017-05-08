<?php

namespace ASPEN;

use Exception;

class Endpoint {
    public $route;
    public $method;
    private $callback;

    private $connector;
    private $response;

    public function __construct($info = []) {
        if (!array_key_exists('to', $info)) throw new Exception('Invalid route given to endpoint.');
        $this->route  = $info['to'];

        if (array_key_exists('method', $info)) $this->method = $info['method'];
        return $this;
    }

    public function then($callback) {
        $this->callback = $callback;
        return $this;
    }

    public function getCallback() {
        return $this->callback;
    }

    public function attachConnector(Connector $c) {
        $this->connector = $c;
        return $this;
    }

    public function getConnector() {
        return $this->connector;
    }

    public function attachResponse(Response $r) {
        $this->response = $r;
        return $this;
    }

    public function getResponse() {
        return $this->response == null ? new Response() : $this->response;
    }

    public function runCallback() {
        $cb = $this->callback;
        $connector = $this->getConnector();

        if ($this->method) {
            if (!$connector->usingMethod($this->method)) return false;
        }

        return $cb($this->getResponse(), $connector);
    }
}

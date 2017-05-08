<?php

namespace ASPEN;

class Endpoint {
    public $route;
    public $method;
    private $callback;

    public function __construct($info = []) {
        $this->route  = $info['to'];
        $this->method = $info['method'];
        return $this;
    }

    public function then($callback) {
        $this->callback = $callback;
        return $this;
    }

    public function getCallback() {
        return $this->callback;
    }

    public function runCallback(Connector $c) {
        $cb = $this->callback;
        if (!$c->usingMethod($this->method)) return false;
        else return $cb(new Response(), $c);
    }
}

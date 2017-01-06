<?php

namespace ASPEN;

class Router {
    private $route = '';
    private $parts = [];
    private $variables = [];

    public function __construct() {
        $this->setRoute(filter_input(INPUT_GET, 'route'));
    }

    public function setRoute($route = '') {
        if (substr($route, -1, 1) != '/') $route .= '/';
        $this->route = $route;

        $parts = explode('/', $route);
        array_pop($parts);
        $this->parts = $parts;
    }

    public function getRoute() {
        return $this->route;
    }

    public function getParts() {
        return $this->parts;
    }

    public function getVariables() {
        return array_merge($this->variables, $this->postVariables());
    }

    public function getVariable($var = '') {
        return $this->variables[$var];
    }

    public function matches($path, $decode = false) {
        if ($path == '') $path = '/';
        if (substr($path, -1, 1) != '/') $path .= '/';

        $parts = explode('/', $path);
        array_pop($parts);

        $expected = count($parts);
        $has = 0;

        if ($expected < count($this->getParts())) return false;

        for ($i = 0; $i < count($parts); $i++) {
            if (!isset($this->getParts()[$i])) break;

            $routePart = $this->getParts()[$i];
            $variable = (substr($parts[$i], 0, 1) == '{' && substr($parts[$i], -1, 1) == '}');

            if (!$variable) {
                if ($parts[$i] == $routePart) $has++;
            } else {
                $expected--;
                $name = str_replace(['{', '}'], '', $parts[$i]);
                $data = $routePart;

                if ($decode && $this->isJSON($data)) $data = json_decode($data, true);

                $this->variables[$name] = $data;
            }
        }

        if ($has > $expected) return false;
        elseif ($expected == $has) return true;
        else return false;
    }

    private function isJSON($string = '') {
        return is_string($string) && is_array(json_decode($string, true)) && json_last_error() == JSON_ERROR_NONE;
    }

    private function postVariables() {
        $vars = filter_input_array(INPUT_POST);
        if (is_array($vars)) return $vars;
        else return [];
    }
}

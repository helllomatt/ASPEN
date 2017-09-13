<?php

namespace ASPEN;

class Router {
    private $route = '';
    private $parts = [];
    private $variables = [];

    public function __construct($route = '') {
        $this->setRoute($route);
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
        return array_merge($this->variables, $this->requestVariables(INPUT_POST), $this->requestVariables(INPUT_GET), $this->requestVariables("input"));
    }

    public function getVariable($var = '') {
        if (!isset($this->variables[$var])) return null;
        return $this->variables[$var];
    }

    public function matches($path, $decode = false) {
        if ($path == '') $path = '/';
        if (substr($path, -1, 1) != '/') $path .= '/';

        $parts = explode('/', $path);
        array_pop($parts);

        $has = 0;
        $expected = 0;
        $varcount = 0;

        for ($i = 0; $i < count($this->getParts()); $i++) {
            if (!isset($parts[$i])) continue;
            if (substr($parts[$i], 0, 1) == ":") {
                $varcount++;
                continue;
            }
            $expected++;
        }

        if ($expected + $varcount < count($this->getParts())) return false;

        for ($i = 0; $i < count($parts); $i++) {
            if (!isset($this->getParts()[$i])) break;

            $routePart = $this->getParts()[$i];
            $variable = (substr($parts[$i], 0, 1) == ':');

            if (!$variable) {
                if ($parts[$i] == $routePart) $has++;
            } else {
                $expected--;
                $name = str_replace(':', '', $parts[$i]);
                $data = $routePart;

                if ($decode && $this->isJSON($data)) $data = json_decode($data, true);

                $this->variables[$name] = $data;
            }
        }

        if ($has > $expected + $varcount) return false;
        elseif ($expected + $varcount == $has) return true;
        else return false;
    }

    private function isJSON($string = '') {
        return is_string($string) && is_array(json_decode($string, true)) && json_last_error() == JSON_ERROR_NONE;
    }

    private function requestVariables($type) {
        $vars = [];

        if ($type === "input" && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], "multipart/form-data") === 0) {
            $vars = $this->parseFormData(file_get_contents("php://input"));
        } elseif ($type === "input" && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], "application/json") === 0) {
            $vars = json_decode(file_get_contents("php://input"), true);
        } elseif (!is_string($type)) {
            $vars = filter_input_array($type);
        }

        if (is_array($vars)) {
            foreach ($vars as $key => $val) {
                if (is_numeric($val)) $vars[$key] += 0;
                if ($val == "true") $vars[$key] = true;
                if ($val == "false") $var[$key] = false;
            }
        }

        if (is_array($vars)) return $vars;
        else return [];
    }

    public function parseFormData($raw) {
        $lines = explode("\n", $raw);

        $outdata = [];
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            if (substr($line, 0, 1) == "-" && $line == "") continue;
            preg_match('/"(.*)"/', $line, $matches);
            if (empty($matches)) continue;

            $vardata = "";
            for ($o = $i + 2; $o < count($lines); $o++) {
                if (substr($lines[$o], 0, 1) == "-") break;
                $vardata .= $lines[$o]."\n";
            }

            $outdata[$matches[1]] = trim($vardata);
        }

        return $outdata;
    }
}

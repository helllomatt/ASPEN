<?php

namespace ASPEN;

class Router {
    private $route = '';
    private $parts = [];
    private $variables = [];


    /**
     * Creates the router with the incoming request path
     *
     * @param string $route
     */
    public function __construct($route = '') {
        $this->setRoute($route);
    }

    /**
     * Defines the route of the endpoint
     *
     * @param string $route
     */
    public function setRoute($route = '') {
        if (substr($route, -1, 1) != '/') $route .= '/';
        $this->route = $route;

        $parts = explode('/', $route);
        array_pop($parts);
        $this->parts = $parts;
    }

    /**
     * Returns the request route
     *
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Returns the request route as an array
     *
     * @return array
     */
    public function getParts() {
        return $this->parts;
    }

    /**
     * Gets any and all variables from an incoming request
     *
     * @return array
     */
    public function getVariables() {
        return array_merge($this->variables, $this->requestVariables(INPUT_POST), $this->requestVariables(INPUT_GET), $this->requestVariables("input"));
    }

    /**
     * Returns a specific variable that was found in the incoming request
     *
     * @param  string $var
     * @return any|NULL
     */
    public function getVariable($var = '') {
        if (!isset($this->variables[$var])) return null;
        return $this->variables[$var];
    }

    /**
     * Checks to see if an endpoint matches the incoming request
     *
     * @param  string  $path
     * @param  boolean $decode
     * @return boolean
     */
    public function matches($path, $decode = false) {
        $route_parts = $this->getParts();
        $route_parts_count = count($route_parts);
        if ($path == '') $path = '/';
        if (substr($path, -1, 1) != '/') $path .= '/';

        // explode the endpoint path
        $parts = explode('/', $path);
        array_pop($parts);

        $has = 0;
        $expected = 0;
        $varcount = 0;

        // loop through the endpoint parts to get a count of the variables
        // and a count of the expected matches
        for ($i = 0; $i < $route_parts_count; $i++) {
            if (!isset($parts[$i])) continue;
            if (substr($parts[$i], 0, 1) == ":") {
                $varcount++;
                continue;
            }
            $expected++;
        }

        if ($expected + $varcount < $route_parts_count) return false;

        // loop through the endpoint path to match up each part and also define
        // the variables on their way
        for ($i = 0; $i < count($parts); $i++) {
            if (!isset($route_parts[$i])) break;

            $routePart = $route_parts[$i];
            $variable = (substr($parts[$i], 0, 1) == ':');

            if (!$variable) {
                if ($parts[$i] == $routePart) $has++;
            } else {
                $expected--; // it's expected to be a variable, not a word so back up
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

    /**
     * Checks to see if a string is JSON or not
     *
     * @param  string  $string
     * @return boolean
     */
    private function isJSON($string = '') {
        return is_string($string) && is_array(json_decode($string, true)) && json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * Gets the incoming  request variables (GET, POST, form-data)
     *
     * @param  long|string $type
     * @return array
     */
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

    /**
     * Parses form data to usable variables
     *
     * @param  string  $raw
     * @return array
     */
    public function parseFormData($raw) {
        $lines = explode("\n", $raw);
        $lines_count = count($lines);

        $outdata = [];
        for ($i = 0; $i < $lines_count; $i++) {
            $line = $lines[$i];
            if (substr($line, 0, 1) == "-" && $line == "") continue;
            preg_match('/"(.*)"/', $line, $matches);
            if (empty($matches)) continue;

            $vardata = "";
            for ($o = $i + 2; $o < $lines_count; $o++) {
                if (substr($lines[$o], 0, 1) == "-") break;
                // add a newline because we took them away when we exploded
                $vardata .= $lines[$o]."\n";
            }

            $outdata[$matches[1]] = trim($vardata);
        }

        return $outdata;
    }
}

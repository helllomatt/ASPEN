<?php

namespace ASPEN;

use ASPEN\Response;
use ASPEN\Database\DB;

class APIManager {
    private $folders = [];
    private $apis = [];
    private $response;

    public function __construct() { return $this; }

    /**
     * Attaches a response to the request to avoid creating unused responses, since
     * there can only be one response
     *
     * @param  Response $r
     * @return ASPEN\APIManager
     */
    public function attachResponse(Response $r) {
        $this->response = $r;
        return $this;
    }

    /**
     * Returns the response, or creates one if one hasn't been attached yet
     *
     * @return ASPEN\Response
     */
    public function getResponse() {
        return $this->response == null ? new Response() : $this->response;
    }

    /**
     * Errors out JSON if something went wrong
     *
     * @param  string $msg
     * @return void
     */
    private function error($msg = '') {
        $response = $this->getResponse();
        $response->error($msg);
    }

    public function getControllerFile($folder) {
        $files = [];
        $ending = "controller.php";

        if (!is_dir($folder)) return [];

        foreach (scandir($folder) as $item) {
            if (substr($item, -strlen($ending)) === $ending) $files[] = $item;
        }

        return $files;
    }

    /**
     * Loads all of the APIs and collects their information to start figuring out
     * all of the right endpoints
     *
     * @param  array   $locations
     * @param  boolean $allowDeath
     * @return ASPEN\APIManager
     */
    public function load(array $locations = [], $allowDeath = true) {
        $fail = [];

        foreach ($locations as $folder) {
            $controllers = $this->getControllerFile($folder);

            if (empty($controllers)) {
                $this->error("API controller not found for '".$folder."'");
                return $this;
            }

            foreach ($controllers as $controller) {
                $path = $folder.'/'.$controller;
                $api = include $path;

                if ($api === 1) {
                    $this->error('Invalid api setup for \''.$folder.'\' (missing return).');
                    return $this;
                } else {
                    $run = $api->run();
                    if (empty($run)) $fail[] = 1;
                    else $fail[] = 0;
                }

                $this->apis[] = $api;
            }
        }

        $countValues = array_count_values($fail);
        if (isset($countValues[1]) && $countValues[1] >= count($fail)) $this->noResponse($allowDeath);

        return $this;
    }

    /**
     * Returns all of the found/registered APIs
     *
     * @return array
     */
    public function getAPIs() {
        return $this->apis;
    }

    /**
     * If no endpoint has been found, return 404
     *
     * @param  boolean $allowDeath
     * @return void
     */
    private function noResponse($allowDeath) {
        if (!headers_sent()) http_response_code(404);
        if ($allowDeath) die();
    }
}

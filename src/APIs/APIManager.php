<?php

namespace ASPEN;

use ASPEN\Response;
use ASPEN\Database\DB;

class APIManager {
    private $folders = [];
    private $apis = [];
    private $response;

    public function __construct() {
        return $this;
    }

    public function attachResponse(Response $r) {
        $this->response = $r;
        return $this;
    }

    public function getResponse() {
        return $this->response == null ? new Response() : $this->response;
    }

    private function error($msg = '') {
        $response = $this->getResponse();
        $response->error($msg);
    }

    public function load(array $locations = [], $allowDeath = true) {
        $fail = [];

        foreach ($locations as $folder) {
            $path = $folder.'/controller.php';

            if (file_exists($path)) $api = include $path;
            else {
                $this->error('api controller not found for \''.$folder.'\'');
                return;
            }

            if ($api === 1) {
                $this->error('Invalid api setup for \''.$folder.'\' (missing return).');
                return;
            } else {
                $run = $api->run();
                if (empty($run)) $fail[] = 1;
                else $fail[] = 0;
            }

            $this->apis[] = $api;
        }

        $countValues = array_count_values($fail);
        if (isset($countValues[1]) && $countValues[1] >= count($fail)) $this->noResponse($allowDeath);

        return $this;
    }

    public function getAPIs() {
        return $this->apis;
    }

    private function noResponse($allowDeath) {
        if (!headers_sent()) http_response_code(404);
        if ($allowDeath) die();
    }
}

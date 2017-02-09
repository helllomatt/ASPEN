<?php

namespace ASPEN;

use ASPEN\Response;
use ASPEN\Database\DB;

class APIManager {
    private $folders = [];
    private $apis = [];
    private $database = null;

    public function __construct() { }

    private function error($msg = '') {
        $response = new Response();
        $response->error($msg);
    }

    public function setDatabase(DB $db = null) {
        $this->database = $db;
    }

    public function load(array $locations = []) {
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
                $api->setDatabase($this->database);
                $run = $api->run();
                if (empty($run)) $fail[] = 1;
                else $fail[] = 0;
            }

            $this->apis[] = $api;
        }

        if (array_count_values($fail)[1] >= count($fail)) $this->noResponse();
    }

    private function noResponse() {
        http_response_code(404);
        die();
    }
}

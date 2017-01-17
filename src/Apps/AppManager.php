<?php

namespace ASPEN;

use ASPEN\Response;
use ASPEN\Database\DB;

class AppManager {
    private $folders = [];
    private $apps = [];
    private $database = null;

    public function __construct() {
        $this->findApps();
    }

    private function findApps() {
        $root = dirname(dirname(dirname(__FILE__)));
        $possible = array_merge(scandir('apps/'), scandir('vendor/'), scandir($root.'/apps'));

        foreach ($possible as $folder) {
            if ($folder == '..' || $folder == '.') continue;
            $builtin_path = $root.'/apps/%s/controller.php';
            $app_path = 'apps/%s/controller.php';
            $ven_path = 'vendor/%s/controller.php';

            if (file_exists(sprintf($builtin_path, $folder)) || file_exists(sprintf($ven_path, $folder)) || file_exists(sprintf($app_path, $folder))) {
                $this->folders[] = $folder;
            }
        }
    }

    private function error($msg = '') {
        $response = new Response();
        $response->error($msg);
    }

    public function getAppFolders() {
        return $this->folders;
    }

    public function getApps() {
        return $this->apps;
    }

    public function setDatabase(DB $db = null) {
        $this->database = $db;
    }

    public function loadApps() {
        $fail = [];

        foreach ($this->getAppFolders() as $folder) {
            $builtin  = dirname(dirname(dirname(__FILE__))).'/apps/'.$folder.'/controller.php';
            $app_path = 'apps/'.$folder.'/controller.php';
            $ven_path = 'vendor/'.$folder.'/controller.php';

            if (file_exists($app_path)) $app = include $app_path;
            elseif (file_exists($ven_path)) $app = include $ven_path;
            elseif (file_exists($builtin)) $app = include $builtin;
            else {
                $this->error('App controller not found for \''.$folder.'\'');
                return;
            }

            if ($app === 1) {
                $this->error('Invalid app setup for \''.$folder.'\' (missing return).');
                return;
            } else {
                $app->setDatabase($this->database);
                $run = $app->run();
                if (empty($run)) $fail[] = 1;
                else $fail[] = 0;
            }

            $this->apps[] = $app;
        }

        if (array_count_values($fail)[1] >= count($fail)) $this->noResponse();
    }

    private function noResponse() {
        http_response_code(404);
        die();
    }
}

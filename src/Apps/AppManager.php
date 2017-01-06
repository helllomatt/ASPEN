<?php

namespace ASPEN;

use ASPEN\Response;
use ASPEN\Database\DB;

class AppManager {
    private $folders = [];
    private $apps = [];
    private $db;

    public function __construct() {
        $this->findApps();
    }

    private function findApps() {
        $possible = array_merge(scandir('apps/'), scandir('vendor/'));

        foreach ($possible as $folder) {
            if ($folder == '..' || $folder == '.') continue;
            $app_path = 'apps/%s/controller.php';
            $ven_path = 'vendor/%s/controller.php';
            if (file_exists(sprintf($app_path, $folder)) || file_exists(sprintf($ven_path, $folder))) {
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

    public function setDatabase(DB $db) {
        $this->database = $db;
    }

    public function loadApps() {
        $nothing = true;

        foreach ($this->getAppFolders() as $folder) {
            $app_path = 'apps/'.$folder.'/controller.php';
            $ven_path = 'vendor/'.$folder.'/controller.php';

            if (file_exists($app_path)) $app = include $app_path;
            elseif (file_exists($ven_path)) $app = include $ven_path;
            else {
                $this->error('App controller not found for \''.$folder.'\'');
                return;
            }

            if ($app === 1) {
                $this->error('Invalid app setup for \''.$folder.'\' (missing return).');
                return;
            } else {
                $app->setDatabase($this->database);
                if ($app->run()) $nothing = false;
            }

            $this->apps[] = $app;
        }

        if ($nothing) $this->noResponse();
    }

    private function noResponse() {
        http_response_code(404);
        die();
    }
}

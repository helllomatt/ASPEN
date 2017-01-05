<?php

namespace ASPEN;

use ASPEN\Database\DB;

class AppManager {
    private $folders = [];
    private $apps = [];
    private $db;

    public function __construct() {
        $this->findApps();
    }

    private function findApps() {
        $possible = scandir('apps/');

        foreach ($possible as $folder) {
            if ($folder == '..' || $folder == '.') continue;
            if (file_exists('apps/'.$folder.'/controller.php')) {
                $this->folders[] = $folder;
            }
        }
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
            $path = 'apps/'.$folder;

            $app = (include $path.'/controller.php');
            $app->setDatabase($this->database);
            if ($app->run()) $nothing = false;

            $this->apps[] = $app;
        }

        if ($nothing) $this->noResponse();
    }

    private function noResponse() {
        http_response_code(404);
        die();
    }
}

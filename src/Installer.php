<?php

namespace ASPEN;

class Installer {
    public static function getRoot() {
        $root = __FILE__;
        for ($i = 0; $i <= 4; $i++) $root = dirname($root);
        return $root;
    }

    public static function createHtaccess() {
        $root = static::getRoot();
        file_put_contents($root.'/.htaccess', "RewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule ^(.*)$ ./index.php?route=$1 [QSA]");
    }

    public static function createIndex() {
        $root = static::getRoot();
        file_put_contents($root.'/index.php', "<?php\n\nrequire 'vendor/autoload.php';\n\n\$manager = new ASPEN\AppManager();\n\$manager->loadApps();");
    }
}

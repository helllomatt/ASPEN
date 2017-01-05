<?php

namespace Authentication;

use ASPEN\Response;

use OAuth2\Request;
use OAuth2\Autoloader;
use OAuth2\Storage\Pdo;
use OAuth2\Server;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\AuthorizationCode;

class Auth {
    private $server = null;

    private $database_name = 'test';
    private $database_host = 'localhost';
    private $database_user = 'root';
    private $database_pass = 'root';

    private $valid = false;

    public function __construct() {
        $this->createServer();
    }

    public function handleTokenRequest() {
        $this->server->handleTokenRequest(Request::createFromGlobals())->send();
    }

    public function validate($throw = false) {
        if ($this->server->verifyResourceRequest(Request::createFromGlobals())) $this->valid = true;
    }

    public function valid() {
        return $this->valid;
    }

    private function createServer() {
        Autoloader::register();

        $storage = new Pdo([
            'dsn'       => $this->dsn(),
            'username'  => $this->database_user,
            'password'  => $this->database_pass
        ], [
            'user_table' => 'users'
        ]);

        $server = new Server($storage);

        $server->addGrantType(new UserCredentials($storage));
        $server->addGrantType(new RefreshToken($storage));
        $server->addGrantType(new AuthorizationCode($storage));

        $this->server = $server;
    }

    private function dsn() {
        return sprintf('mysql:dbname=%s;host%s;', $this->database_name, $this->database_host);
    }
}

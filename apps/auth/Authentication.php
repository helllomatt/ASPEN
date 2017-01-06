<?php

namespace Authentication;

use ASPEN\Response;

use OAuth2\Request;
use OAuth2\Autoloader;
use OAuth2\Server;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\AuthorizationCode;

class Auth {
    private $server     = null;
    private $storage    = null;

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

    public function requireValidToken() {
        $this->validate();
        if (!$this->valid()) {
            $response = new Response();
            $response->error('Unauthorized.');
            die();
        }
    }

    public function getStorage() {
        return $this->storage;
    }

    public function getServer() {
        return $this->server;
    }

    public function getToken() {
        return $this->server->getAccessTokenData(Request::createFromGlobals());
    }

    public function getUser() {
        $token = $this->getToken();
        return $this->storage->getUserDetails($token['user_id']);
    }

    public function requirePermission($permission = '') {
        $this->requireValidToken();
        $user = $this->getUser();
        if (!$user || !in_array($permission, $user['permissions'])) {
            $response = new Response();
            $response->error('Invalid permission');
            die();
        }

        return true;
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

        $this->server   = $server;
        $this->storage  = $storage;
    }

    private function dsn() {
        return sprintf('mysql:dbname=%s;host%s;', $this->database_name, $this->database_host);
    }
}

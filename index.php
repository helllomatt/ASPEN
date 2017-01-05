<?php

//header('Content-Type: application/json');
require 'vendor/autoload.php';

$database = new ASPEN\Database\DB();
$database->connect('localhost', 'root', 'root', 'test');

$manager = new ASPEN\AppManager();
$manager->setDatabase($database);
$manager->loadApps();

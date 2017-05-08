<?php

namespace ASPEN;

class ConfigTest extends \PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
        file_put_contents('tests/config.json', json_encode(["databases" => ["main" => ["host" => "localhost", "db" => "test", "username" => "root", "password" => ""]]]));
    }

    public static function tearDownAfterClass() {
        unlink('tests/config.json');
    }

    public function testLoadMissingConfig() {
        $this->expectException('\Exception');
        Config::load('null');
    }

    public function testLoadConfig() {
        Config::load('tests/config.json');
    }

    public function testGettingKey() {
        $this->assertArrayHasKey('main', Config::get('databases'));
    }

    public function testGettingDBInfo() {
        $this->assertEquals(["host" => "localhost", "db" => "test", "username" => "root", "password" => ""], Config::getDBConfig('main'));
    }
}

<?php

namespace ASPEN;

class EndpointTest extends \PHPUnit_Framework_TestCase {
    public function testConstructing() {
        $endpoint = new Endpoint([
            'to' => 'get/',
            'method' => 'get'
        ]);

        $this->assertEquals('get/', $endpoint->route);
        $this->assertEquals('get', $endpoint->method);
    }

    public function testBlankConstructor() {
        $this->expectException('\Exception');
        $endpoint = new Endpoint();
    }

    public function testAnyMethod() {
        $endpoint = new Endpoint(['to' => 'get/']);

        $this->assertNull($endpoint->method);
    }

    public function testSettingCallback() {
        $endpoint = (new Endpoint(['to' => 'get/']))
            ->then(function() { return true; });

        $this->assertEquals(function() { return true; }, $endpoint->getCallback());
    }

    public function testRunningCallback() {
        $endpoint = (new Endpoint(['to' => 'get/']))
            ->then(function() { return true; });

        $this->assertTrue($endpoint->runCallback());
    }
}

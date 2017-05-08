<?php

namespace ASPEN;

class APITest extends \PHPUnit_Framework_TestCase {
    public function testConstructing() {
        $router = $this->getMock('ASPEN\Router');
        $api = (new API('name', $router))->version(1);

        $this->assertEquals('name', $api->getName());
        $this->assertEquals(1, $api->getVersion());
    }

    public function testAddingEndpoint() {
        $router = $this->getMock('ASPEN\Router', ['matches']);
        $router->expects($this->once())->method('matches')->will($this->returnValue(true));

        $endpoint = $this->getMock('ASPEN\Endpoint', [], [['to' => 'test']]);

        $api = (new API('name', $router))->version(1);
        $api->add($endpoint);

        $this->assertEquals($endpoint, $api->getEndpoints()[0]);
    }

    public function testRunningEndpoint() {
        $router = $this->getMock('ASPEN\Router', ['matches', 'getVariables']);
        $router->expects($this->once())->method('matches')->will($this->returnValue(true));

        $connector = $this->getMock('ASPEN\Connector', ['setData']);

        $endpoint = $this->getMock('ASPEN\Endpoint', ['runCallback'], [['to' => 'test']]);
        $endpoint->expects($this->once())->method('runCallback')->will($this->returnValue(function() { return true; }));

        $api = (new API('name', $router))->version(1);
        $api->add($endpoint);

        $this->assertEquals([true], $api->run($connector));
    }
}

<?php

namespace ASPEN;

class RouterTest extends \PHPUnit_Framework_TestCase {
    public function testConstructing() {
        $router = new Router('v1/endpoint');
        $this->assertEquals('v1/endpoint/', $router->getRoute());
        $this->assertEquals(['v1', 'endpoint'], $router->getParts());
    }

    public function testMatching() {
        $router = new Router('v1/endpoint/');
        $this->assertTrue($router->matches('v1/endpoint/'));
    }

    public function testMatchingWithVariables() {
        $router = new Router('v1/endpoint/asdf');
        $this->assertTrue($router->matches('v1/endpoint/{variable}'));
    }

    public function testMatchingWithMissingVariable() {
        $router = new Router('v1/endpoint/');
        $this->assertFalse($router->matches('v1/endpoint/{variable}'));
    }

    public function testGettingVariables() {
        $router = new Router('v1/endpoint/asdf');
        $router->matches('v1/endpoint/{variable}');

        $this->assertEquals('asdf', $router->getVariable('variable'));
    }
}

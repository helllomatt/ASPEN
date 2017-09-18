<?php

namespace ASPEN;

class ResponseTest extends \PHPUnit_Framework_TestCase {
    public function testConstructing() {
        $response = (new Response())->ignoreCount();

        $this->assertEquals(['status' => 'fail', 'data' => []], $response->getRaw());
    }

    public function testSettingStatus() {
        $response = (new Response())
            ->ignoreCount()
            ->status('success');

        $this->assertEquals('success', $response->getStatus());
    }

    public function testSettingData() {
        $response = (new Response())->ignoreCount();
        $response->add('key', 'value');

        $this->assertEquals(['key' => 'value'], $response->getData());
    }

    public function testSuccessfulResponse() {
        $this->expectOutputString(json_encode(['status' => 'success', 'data' => []]));

        $response = (new Response())->ignoreCount();
        $response->success();
    }

    public function testSuccessfulResponseWithData() {
        $this->expectOutputString(json_encode(['status' => 'success', 'data' => ['key' => 'value']]));

        $response = (new Response())->ignoreCount();
        $response->add('key', 'value');
        $response->success();
    }

    public function testErrorResponse() {
        $this->expectOutputString(json_encode(['status' => 'error', 'error' => 'error', 'message' => 'error message']));

        $response = (new Response())->ignoreCount();
        $response->error('error', 200, 'error message');
    }

    public function testFailedResponse() {
        $this->expectOutputString(json_encode(['status' => 'fail', 'data' => []]));

        $response = (new Response())->ignoreCount();
        $response->fail();
    }

    public function testFailedResponseWithData() {
        $this->expectOutputString(json_encode(['status' => 'fail', 'data' => ['key' => 'value']]));

        $response = (new Response())->ignoreCount();
        $response->add('key', 'value');
        $response->fail();
    }
}

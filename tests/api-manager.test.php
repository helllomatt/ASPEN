<?php

namespace ASPEN;

class APIManagerTest extends \PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
        mkdir('tests/bad-test-module');
        mkdir('tests/test-module');
        file_put_contents('tests/bad-test-module/controller.php', '<?php $api = (new ASPEN\API("bad"))->version(1);');
        file_put_contents('tests/test-module/controller.php', '<?php $api = (new ASPEN\API("good"))->version(1); return $api;');
        file_put_contents("tests/test-module/v2.controller.php", "");
    }

    public static function tearDownAfterClass() {
        unlink("tests/test-module/v2.controller.php");
        unlink('tests/test-module/controller.php');
        unlink('tests/bad-test-module/controller.php');
        rmdir('tests/test-module');
        rmdir('tests/bad-test-module');
    }

    public function responseErrorCallback() {
        $args = func_get_args();
        echo json_encode(['status' => 'error', 'message' => $args[0]]);
    }

    public function testNonExistingLoading() {
        $response = $this->getMock('ASPEN\Response', ['error']);
        $response->expects($this->once())->method('error')->will($this->returnCallback([$this, 'responseErrorCallback']));
        $this->expectOutputString(json_encode(['status' => 'error', 'message' => 'API controller not found for \'null\'']));
        $manager = (new APIManager())->attachResponse($response)->load(['null']);
    }

    public function testNoReturnLoading() {
        $response = $this->getMock('ASPEN\Response', ['error']);
        $response->expects($this->once())->method('error')->will($this->returnCallback([$this, 'responseErrorCallback']));
        $this->expectOutputString(json_encode(['status' => 'error', 'message' => 'Invalid api setup for \'tests/bad-test-module\' (missing return).']));
        $manager = (new APIManager())->attachResponse($response)->load(['tests/bad-test-module']);
    }

    public function testLoading() {
        $response = $this->getMock('ASPEN\Response');
        $manager = (new APIManager())->attachResponse($response)->load(['tests/test-module'], false);
        $this->assertEquals('good', $manager->getApis()[0]->getName());
    }

    public function testGettingControllerFile() {
        $manager = (new APIManager());
        $controller_files = $manager->getControllerFile("./tests/test-module/");

        $this->assertEquals(["controller.php", "v2.controller.php"], $controller_files);
    }
}

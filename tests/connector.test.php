<?php

namespace ASPEN;

class ConnectorTest extends \PHPUnit_Framework_TestCase {
    public function testSettingData() {
        $connector = new Connector();
        $connector->setData(['key' => 'value']);

        $this->assertEquals('value', $connector->getVariable('key'));
    }
}

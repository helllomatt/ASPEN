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
        $this->assertTrue($router->matches('v1/endpoint/:variable'));
    }

    public function testMatchingWithMissingVariable() {
        $router = new Router('v1/endpoint/');
        $this->assertTrue($router->matches('v1/endpoint/:variable'));
        $this->assertNull($router->getVariable("variable"));
    }

    public function testGettingVariables() {
        $router = new Router('v1/endpoint/asdf');
        $router->matches('v1/endpoint/:variable');

        $this->assertEquals('asdf', $router->getVariable('variable'));
    }

    public function testParsingFormData() {
        $router = new Router('v1/endpoint/asdf');

        $formdata = "----------------------------123456789012345678901234
Content-Disposition: form-data; name=\"name\"

John Smith
----------------------------123456789012345678901234
Content-Disposition: form-data; name=\"age\"

25
----------------------------123456789012345678901234--";

        $expected = [ "name" => "John Smith", "age" => 25 ];
        $this->assertEquals($expected, $router->parseFormData($formdata));
    }

    public function testParsingMultiLineFormData() {
            $router = new Router('v1/endpoint/asdf');

            $formdata = "----------------------------123456789012345678901234
Content-Disposition: form-data; name=\"ipsum\"

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sit amet leo in dui rutrum suscipit. Nunc et enim congue, consequat nunc non, auctor ligula. Integer lobortis sodales velit condimentum pellentesque. Ut aliquam sapien non sollicitudin tincidunt. Mauris egestas semper leo in pharetra. Maecenas ultricies eu nisi non scelerisque. Proin condimentum diam non nibh scelerisque facilisis. Quisque efficitur volutpat felis non venenatis. Donec in lectus in erat porttitor fermentum nec non ipsum. Aenean ornare eros ut lectus dignissim facilisis. Maecenas vestibulum iaculis nisi vel ultricies. Quisque ac aliquet lectus, sed vehicula sapien. Aliquam sodales urna a purus commodo condimentum.

Ut blandit sagittis quam, ut fringilla nulla ultricies vel. Vivamus vulputate turpis consectetur nisl pretium ornare. Nunc semper fermentum fringilla. Quisque eget diam mollis, bibendum augue in, venenatis nulla. Praesent ac mauris semper, ultrices augue id, pulvinar est. Integer vitae gravida nunc. In dapibus faucibus varius. Nulla velit enim, ultrices et iaculis sed, gravida id turpis. Fusce rhoncus, justo sit amet venenatis luctus, libero nisl mattis lacus, at pellentesque urna dolor sed massa. Nulla id odio nunc. Suspendisse posuere convallis mi, quis varius purus porta in.

Sed nec arcu condimentum, aliquam ligula id, maximus augue. Vestibulum id blandit nisi. Etiam dictum nisl dolor, sed posuere lacus vestibulum et. Maecenas porta a magna faucibus lacinia. Quisque interdum nisi est, posuere luctus nulla cursus sed. Maecenas a libero tellus. Aenean tincidunt consequat turpis, sed dictum eros congue ut. Morbi at nibh a lorem maximus tincidunt.

Quisque rhoncus mi nibh. Donec dignissim, urna id faucibus fermentum, justo turpis placerat metus, ac gravida nunc arcu ac justo. Suspendisse ac tristique neque, ut egestas sem. Ut posuere laoreet elit, nec ullamcorper lectus cursus at. Nunc ex est, dictum aliquet augue vitae, porta vestibulum sem. In pharetra lacus mi, molestie vehicula ex gravida sodales. Quisque feugiat mauris sit amet aliquet condimentum. Nam fringilla ullamcorper dolor. Integer elit enim, pellentesque sit amet tempus in, ultricies a augue. Suspendisse congue diam lacus, eu viverra neque egestas quis. Sed porta purus eu elit fringilla, sed volutpat dolor porta. Morbi tempor pretium diam, ac lacinia nisi. Vivamus libero felis, bibendum ut purus molestie, tincidunt blandit nunc. Fusce feugiat vestibulum turpis sit amet eleifend.

Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam in orci dapibus, blandit est a, scelerisque diam. Sed congue leo enim, sit amet lacinia velit facilisis sit amet. In hac habitasse platea dictumst. Donec ut sagittis mi. Aenean elementum scelerisque nibh sit amet scelerisque. Integer imperdiet, diam non convallis finibus, augue ante semper elit, nec viverra magna tortor a magna. Nullam cursus nulla mauris, sed volutpat est euismod nec. Integer pellentesque varius risus ut ultricies. Phasellus in semper velit.
----------------------------123456789012345678901234--";

            $expected = [ "ipsum" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sit amet leo in dui rutrum suscipit. Nunc et enim congue, consequat nunc non, auctor ligula. Integer lobortis sodales velit condimentum pellentesque. Ut aliquam sapien non sollicitudin tincidunt. Mauris egestas semper leo in pharetra. Maecenas ultricies eu nisi non scelerisque. Proin condimentum diam non nibh scelerisque facilisis. Quisque efficitur volutpat felis non venenatis. Donec in lectus in erat porttitor fermentum nec non ipsum. Aenean ornare eros ut lectus dignissim facilisis. Maecenas vestibulum iaculis nisi vel ultricies. Quisque ac aliquet lectus, sed vehicula sapien. Aliquam sodales urna a purus commodo condimentum.

Ut blandit sagittis quam, ut fringilla nulla ultricies vel. Vivamus vulputate turpis consectetur nisl pretium ornare. Nunc semper fermentum fringilla. Quisque eget diam mollis, bibendum augue in, venenatis nulla. Praesent ac mauris semper, ultrices augue id, pulvinar est. Integer vitae gravida nunc. In dapibus faucibus varius. Nulla velit enim, ultrices et iaculis sed, gravida id turpis. Fusce rhoncus, justo sit amet venenatis luctus, libero nisl mattis lacus, at pellentesque urna dolor sed massa. Nulla id odio nunc. Suspendisse posuere convallis mi, quis varius purus porta in.

Sed nec arcu condimentum, aliquam ligula id, maximus augue. Vestibulum id blandit nisi. Etiam dictum nisl dolor, sed posuere lacus vestibulum et. Maecenas porta a magna faucibus lacinia. Quisque interdum nisi est, posuere luctus nulla cursus sed. Maecenas a libero tellus. Aenean tincidunt consequat turpis, sed dictum eros congue ut. Morbi at nibh a lorem maximus tincidunt.

Quisque rhoncus mi nibh. Donec dignissim, urna id faucibus fermentum, justo turpis placerat metus, ac gravida nunc arcu ac justo. Suspendisse ac tristique neque, ut egestas sem. Ut posuere laoreet elit, nec ullamcorper lectus cursus at. Nunc ex est, dictum aliquet augue vitae, porta vestibulum sem. In pharetra lacus mi, molestie vehicula ex gravida sodales. Quisque feugiat mauris sit amet aliquet condimentum. Nam fringilla ullamcorper dolor. Integer elit enim, pellentesque sit amet tempus in, ultricies a augue. Suspendisse congue diam lacus, eu viverra neque egestas quis. Sed porta purus eu elit fringilla, sed volutpat dolor porta. Morbi tempor pretium diam, ac lacinia nisi. Vivamus libero felis, bibendum ut purus molestie, tincidunt blandit nunc. Fusce feugiat vestibulum turpis sit amet eleifend.

Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam in orci dapibus, blandit est a, scelerisque diam. Sed congue leo enim, sit amet lacinia velit facilisis sit amet. In hac habitasse platea dictumst. Donec ut sagittis mi. Aenean elementum scelerisque nibh sit amet scelerisque. Integer imperdiet, diam non convallis finibus, augue ante semper elit, nec viverra magna tortor a magna. Nullam cursus nulla mauris, sed volutpat est euismod nec. Integer pellentesque varius risus ut ultricies. Phasellus in semper velit." ];
            $this->assertEquals($expected, $router->parseFormData($formdata));
    }

    public function testParsingFDWithURLVars() {
        $router = new Router('v1/endpoint/asdf/1');
        $router->matches('v1/endpoint/asdf/:id');

        $formdata = "----------------------------787294457202242511083478
Content-Disposition: form-data; name=\"name\"

John Smith
----------------------------787294457202242511083478
Content-Disposition: form-data; name=\"age\"

25
----------------------------787294457202242511083478--";

        $parseFormData = $router->parseFormData($formdata);
        $expected = [ "name" => "John Smith", "id" => 1, "age" => 25 ];
        $actual = [ "name" => $parseFormData["name"], "id" => $router->getVariable("id"), "age" => $parseFormData["age" ] ];
        $this->assertEquals($expected, $actual);
    }
}

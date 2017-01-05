# Apps

## Folder structure

```
apps/{appname}
apps/{appname}/controller.php
```

## Example controller

```php
<?php

$app = new ASPEN\App('Example App');
$app->version('1'); // api/v1

// route handling
$app->get('example/', function() {
    $response = new ASPEN\Response();
    $response->add('message', 'Example API response!');
    $response->success();
});

$app->get('example/say-hello/{name}', function(ASPEN\Connector $c) {
    new ExampleApp\ExampleClass($c->getVariable('name'));
    // automatically responds 'Hello {name}'
});

return $app; // return for interaction and running later on
```

## Autoloading

Add the namespace to the composer autoloader. In `composer.json` under `autoload.psr-4` add the namespace and the path, then run `composer dumpautoload -o` to turn it on

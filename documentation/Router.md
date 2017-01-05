# Router

### Usage

```php
<?php

$router = new ASPEN\Router();

$router->getRoute(); // returns the current route defined in the browser url
$router->setRoute($to); // allows you to set a custom route
$router->getParts(); // returns the current route split into an array

$router->matches($path, $decode); // returns a boolean on whether the current route matches the given path
// setting 'decode' to true will automatically decode any JSON found variables

$router->getVariables(); // returns an array of all the variables found in the route
$router->getVariable($key); // returns the value of a variable from the URL
```

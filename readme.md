# ASPEN
<a href='https://travis-ci.org/helllomatt/ASPEN'><img src='https://travis-ci.org/helllomatt/ASPEN.svg?branch=master' /></a>

Aspen is and API manager and router. Make a request, and it will handle where to go for processing and returning information.

## Installing

```
composer require helllomatt/aspen
```

## Setting up

You need to create an index file for your project. This will load your config file and also the api endpoints you would like to use.

`index.php`

```php
<?php

namespace ASPEN;

require './vendor/autoload.php';

// If you're interested in enabling CORS
// Config::checkOrigin(Config::getOriginInformation());

Config::load('path/to/config.json');
$manager = new APIManager();
$manager->load([
    'modules/endpoint1',
    'modules/endpoint2'
]);
```

If you're using Apache, a `.htaccess` file is required for clean routing.

`.htaccess`

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ ./index.php?route=$1 [QSA]
```

If you're using Nginx, you need to modify your site configuration.

[todo]


## Creating endpoint

Modules, plugins, addons, or whatever you call them are the separated endpoints to your API. When you define the modules to load in the index file you created above, everything there is an absolute path. The `modules/` folder is just a suggestion, you can organize it however you want.

Create the `modules/` folder then inside of there create a `sample/` folder, leaving the full path to `[ROOT]/modules/sample/`.

There's a required file for an API called `controller.php`. That does what you think it does, controls the routing.

`controller.php`

```
<?php

use ASPEN\API;
use ASPEN\Response;

$api = new API('Sample'); // API name (not really important)
$api->version(1);         // API version (important)

// API endpoints
$api->add((new Endpoint([
        'to'     => 'test/'
        'method' => 'get'
    ]))->then(function(Response $r) {
        $r->add("hello", "world");
        $r->success();
    }))

return $api; // VERY IMPORTANT!
```

Create the API object, which is just that. Then set the version. The version is important for when an endpoint is called, the version is looked at. (e.g. `localhost/some-api/v1/get` is different than `localhost/some-api/v2/get`)

Then you create your endpoints, or places that can be accessed.

```
curl http://localhost/aspen/v1/test

[outputs]
{
    "status": "success"
    "data": {
        "hello": "world"
    }
}
```

Lastly and most importantly you need to return the API object out of the file. An example of the innerworkings for this are `$api = (include 'controller.php');`. That lets ASPEN access the API object and work with it when routing.

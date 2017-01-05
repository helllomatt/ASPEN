# Responding

### Usage

```php

<?php

$response = new Response();

// add data to the response
$response->add('key', 'value');

$response->success(); // successful response

$response->fail(); // failed response

$response->error($message, $code, $includeData); // error response

```

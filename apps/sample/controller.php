<?php

$app = new ASPEN\App('Sample');
$app->version('1');

$app->get('', function() {
    $response = new ASPEN\Response();
    $response->add('greeting', 'Hello!');
    $response->add('content', 'This is a sample API call, you\'re at the index right now, so you might want to remove this sample app and create your own! Read more on how to set up an app by visiting the documentation folder at the root of this installation!');

    $response->success();
});

return $app;

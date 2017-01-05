# Authenticating

### Logging in
__Path__ `oauth2/token/`
__Authorization__ `Authorization: Basic username:password`
__Grant Type__ `password`


#### Example (jquery)
```js
$.ajax({
    type:   'post',
    url:    'http://localhost/aspen/v1/oauth2/token',
    beforeSend: function(xhr) {
        xhr.setRequestHeader('Authorization', 'Basic ' + btoa('<client_id>:<client_secret>'));
    },
    data:   {
        grant_type: 'password',
        username:   '<username>',
        password:   '<password>',
    },
    success:    function(data) {
        console.log(data);
    }
});
```

### Refreshing
__Path__ `oauth2/token`
__Authorization__ `Authorization: Basic username:password`
__Grant Type__ `refresh_token`

#### Example (jquery)
```js
$.ajax({
    type:   'post',
    url:    'http://localhost/aspen/v1/oauth2/token',
    beforeSend: function(xhr) {
        xhr.setRequestHeader('Authorization', 'Basic ' + btoa('<client_id>:<client_secret>'));
    },
    data:   {
        grant_type:     'refresh_token',
        refresh_token:  '<refresh_token>'
    },
    success:    function(data) {
        console.log(data);
    }
});
```

### Validating and Usage
__Path__ `oauth2/validate`
__Authorization__ `Authorization: Bearer <access_token>`

#### Example (jquery)
```js
$.ajax({
    type:   'get',
    url:    'http://localhost/aspen/v1/oauth2/validate',
    beforeSend: function(xhr) {
        xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },
    success:    function(data) {
        console.log(data);
    }
});
```

### Requiring validation server-side
```php
<?php

$auth = new Authentication\Auth();
$auth->validate();

$response = new Response();
var_dump($auth->valid())
```

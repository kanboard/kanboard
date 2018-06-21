JsonRPC PHP Client and Server
=============================

A simple Json-RPC client/server that just works.

Features
--------

- JSON-RPC 2.0 only
- The server support batch requests and notifications
- Authentication and IP based client restrictions
- Custom Middleware
- Fully unit tested
- Requirements: PHP >= 5.3.4
- License: MIT

Author
------

Frédéric Guillot

Installation with Composer
--------------------------

```bash
composer require fguillot/json-rpc @stable
```

Examples
--------

### Server

Callback binding:

```php
<?php

use JsonRPC\Server;

$server = new Server();
$server->getProcedureHandler()
    ->withCallback('addition', function ($a, $b) {
        return $a + $b;
    })
    ->withCallback('random', function ($start, $end) {
        return mt_rand($start, $end);
    })
;

echo $server->execute();
```

Callback binding from array:

```php
<?php

use JsonRPC\Server;

$callbacks = array(
    'getA' => function() { return 'A'; },
    'getB' => function() { return 'B'; },
    'getC' => function() { return 'C'; }
);

$server = new Server();
$server->getProcedureHandler()->withCallbackArray($callbacks);

echo $server->execute();
```

Class/Method binding:

```php
<?php

use JsonRPC\Server;

class Api
{
    public function doSomething($arg1, $arg2 = 3)
    {
        return $arg1 + $arg2;
    }
}

$server = new Server();
$procedureHandler = $server->getProcedureHandler();

// Bind the method Api::doSomething() to the procedure myProcedure
$procedureHandler->withClassAndMethod('myProcedure', 'Api', 'doSomething');

// Use a class instance instead of the class name
$procedureHandler->withClassAndMethod('mySecondProcedure', new Api, 'doSomething');

// The procedure and the method are the same
$procedureHandler->withClassAndMethod('doSomething', 'Api');

// Attach the class, the client will be able to call directly Api::doSomething()
$procedureHandler->withObject(new Api());

echo $server->execute();
```

Class/Method binding from array:

```php
<?php

use JsonRPC\Server;

class MathApi
{
    public function addition($arg1, $arg2)
    {
        return $arg1 + $arg2;
    }

    public function subtraction($arg1, $arg2)
    {
        return $arg1 - $arg2;
    }

    public function multiplication($arg1, $arg2)
    {
        return $arg1 * $arg2;
    }

    public function division($arg1, $arg2)
    {
        return $arg1 / $arg2;
    }
}

$callbacks = array(
    'addition'       => array( 'MathApi', addition ),
    'subtraction'    => array( 'MathApi', subtraction ),
    'multiplication' => array( 'MathApi', multiplication ),
    'division'       => array( 'MathApi', division )
);

$server = new Server();
$server->getProcedureHandler()->withClassAndMethodArray($callbacks);

echo $server->execute();
```

Server Middleware:

Middleware might be used to authenticate and authorize the client.
They are executed before each procedure.

```php
<?php

use JsonRPC\Server;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Exception\AuthenticationFailureException;

class Api
{
    public function doSomething($arg1, $arg2 = 3)
    {
        return $arg1 + $arg2;
    }
}

class MyMiddleware implements MiddlewareInterface
{
    public function execute($username, $password, $procedureName)
    {
        if ($username !== 'foobar') {
            throw new AuthenticationFailureException('Wrong credentials!');
        }
    }
}

$server = new Server();
$server->getMiddlewareHandler()->withMiddleware(new MyMiddleware());
$server->getProcedureHandler()->withObject(new Api());
echo $server->execute();
```

You can raise a `AuthenticationFailureException` when the API credentials are wrong or a `AccessDeniedException` when the user is not allowed to access to the procedure.

### Client

Example with positional parameters:

```php
<?php

use JsonRPC\Client;

$client = new Client('http://localhost/server.php');
$result = $client->execute('addition', [3, 5]);
```

Example with named arguments:

```php
<?php

use JsonRPC\Client;

$client = new Client('http://localhost/server.php');
$result = $client->execute('random', ['end' => 10, 'start' => 1]);
```

Arguments are called in the right order.

Examples with the magic method `__call()`:

```php
<?php

use JsonRPC\Client;

$client = new Client('http://localhost/server.php');
$result = $client->random(50, 100);
```

The example above use positional arguments for the request and this one use named arguments:

```php
$result = $client->random(['end' => 10, 'start' => 1]);
```

### Client batch requests

Call several procedures in a single HTTP request:

```php
<?php

use JsonRPC\Client;

$client = new Client('http://localhost/server.php');

$results = $client->batch()
                  ->foo(['arg1' => 'bar'])
                  ->random(1, 100)
                  ->add(4, 3)
                  ->execute('add', [2, 5])
                  ->send();

print_r($results);
```

All results are stored at the same position of the call.

### Client exceptions

Client exceptions are normally thrown when an error is returned by the server. You can change this behaviour by
using the `$returnException` argument which causes exceptions to be returned. This can be extremely useful when
executing the batch request. 

- `BadFunctionCallException`: Procedure not found on the server
- `InvalidArgumentException`: Wrong procedure arguments
- `JsonRPC\Exception\AccessDeniedException`: Access denied
- `JsonRPC\Exception\ConnectionFailureException`: Connection failure
- `JsonRPC\Exception\ServerErrorException`: Internal server error

### Enable client debugging

You can enable the debug mode to see the JSON request and response:

```php
<?php

use JsonRPC\Client;

$client = new Client('http://localhost/server.php');
$client->getHttpClient()->withDebug();
```

The debug output is sent to the PHP system logger.
You can configure the log destination in your `php.ini`.

Output example:

```json
==> Request:
{
    "jsonrpc": "2.0",
    "method": "removeCategory",
    "id": 486782327,
    "params": [
        1
    ]
}
==> Response:
{
    "jsonrpc": "2.0",
    "id": 486782327,
    "result": true
}
```

### IP based client restrictions

The server can allow only some IP addresses:

```php
<?php

use JsonRPC\Server;

$server = new Server;

// IP client restrictions
$server->allowHosts(['192.168.0.1', '127.0.0.1']);

[...]

// Return the response to the client
echo $server->execute();
```

If the client is blocked, you got a 403 Forbidden HTTP response.

### HTTP Basic Authentication

If you use HTTPS, you can allow client by using a username/password.

```php
<?php

use JsonRPC\Server;

$server = new Server;

// List of users to allow
$server->authentication(['user1' => 'password1', 'user2' => 'password2']);

[...]

// Return the response to the client
echo $server->execute();
```

On the client, set credentials like that:

```php
<?php

use JsonRPC\Client;

$client = new Client('http://localhost/server.php');
$client->getHttpClient()
    ->withUsername('Foo')
    ->withPassword('Bar');
```

If the authentication failed, the client throw a RuntimeException.

Using an alternative authentication header:

```php

use JsonRPC\Server;

$server = new Server();
$server->setAuthenticationHeader('X-Authentication');
$server->authentication(['myusername' => 'mypassword']);
```

The example above will use the HTTP header `X-Authentication` instead of the standard `Authorization: Basic [BASE64_CREDENTIALS]`.
The username/password values need be encoded in base64: `base64_encode('username:password')`.

### Local Exceptions

By default, the server will relay all exceptions to the client.
If you would like to relay only some of them, use the method `Server::withLocalException($exception)`:

```php
<?php

use JsonRPC\Server;
class MyException1 extends Exception {};
class MyException2 extends Exception {};

$server = new Server();

// Exceptions that should NOT be relayed to the client, if they occurs
$server
    ->withLocalException('MyException1')
    ->withLocalException('MyException2')
;

[...]

echo $server->execute();
```

### Callback before client request

You can use a callback to change the HTTP headers or the URL before to make the request to the server.

Example:

```php
<?php

$client = new Client();
$client->getHttpClient()->withBeforeRequestCallback(function(HttpClient $client, $payload) {
    $client->withHeaders(array('Content-Length: '.strlen($payload)));
});

$client->myProcedure(123);
```

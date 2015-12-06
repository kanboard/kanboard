API Authentication
==================

Default method (HTTP Basic)
---------------------------

The API credentials are available on the settings page.

- API end-point: `https://YOUR_SERVER/jsonrpc.php`

If you want to use the "application api":

- Username: `jsonrpc`
- Password: API token on the settings page

Otherwise for the "user api", just use the real username/passsword.

The API use the [HTTP Basic Authentication Scheme described in the RFC2617](http://www.ietf.org/rfc/rfc2617.txt).
If there is an authentication error, you will receive the HTTP status code `401 Not Authorized`.

### Authorized User API procedures

- getMe
- getMyDashboard
- getMyActivityStream
- createMyPrivateProject
- getMyProjectsList
- getMyProjects
- getTimezone
- getVersion
- getDefaultTaskColor
- getDefaultTaskColors
- getColorList
- getProjectById
- getTask
- getTaskByReference
- getAllTasks
- openTask
- closeTask
- moveTaskPosition
- createTask
- updateTask
- getBoard
- getProjectActivity
- getMyOverdueTasks

Custom HTTP header
------------------

You can use an alternative HTTP header for the authentication if your server have a very specific configuration.

- The header name can be anything you want, by example `X-API-Auth`.
- The header value is the `username:password` encoded in Base64.

Configuration:

1. Define your custom header in your `config.php`: `define('API_AUTHENTICATION_HEADER', 'X-API-Auth');`
2. Encode the credentials in Base64, example with PHP `base64_encode('jsonrpc:19ffd9709d03ce50675c3a43d1c49c1ac207f4bc45f06c5b2701fbdf8929');`
3. Test with curl:

```bash
curl \
-H 'X-API-Auth: anNvbnJwYzoxOWZmZDk3MDlkMDNjZTUwNjc1YzNhNDNkMWM0OWMxYWMyMDdmNGJjNDVmMDZjNWIyNzAxZmJkZjg5Mjk=' \
-d '{"jsonrpc": "2.0", "method": "getAllProjects", "id": 1}' \
http://localhost/kanboard/jsonrpc.php
```

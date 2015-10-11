Json-RPC API
============

User and application API
------------------------

There are two types of API access:

### Application API

- Access to the API with the user "jsonrpc" and the token available in settings
- Access to all procedures
- No permission checked
- There is no user session on the server
- Example of possible clients: tools to migrate/import data, create tasks from another system, etc...

### User API

- Access to the API with the user credentials (username and password)
- Access to a restricted set of procedures
- The project permissions are checked
- A user session is created on the server
- Example of possible clients: mobile/desktop application, command line utility, etc...

Security
--------

- Always use HTTPS with a valid certificate
- If you make a mobile application, it's your job to store securely the user credentials on the device
- After 3 authentication failure on the user api, the end-user have to unlock his account by using the login form
- Two factor authentication is not yet available through the API

Protocol
--------

Kanboard use the protocol Json-RPC to interact with external programs.

JSON-RPC is a remote procedure call protocol encoded in JSON.
Almost the same thing as XML-RPC but with the JSON format.

We use the [version 2 of the protocol](http://www.jsonrpc.org/specification).
You must call the API with a `POST` HTTP request.

Kanboard support batch requests, so you can make multiple API calls in a single HTTP request. It's particularly useful for mobile clients with higher network latency.

Authentication
--------------

### Default method (HTTP Basic)

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

### Custom HTTP header

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

Examples
--------

### Example with cURL

From the command line:

```bash
curl \
-u "jsonrpc:19ffd9709d03ce50675c3a43d1c49c1ac207f4bc45f06c5b2701fbdf8929" \
-d '{"jsonrpc": "2.0", "method": "getAllProjects", "id": 1}' \
http://localhost/kanboard/jsonrpc.php
```

Response from the server:

```json
{
    "jsonrpc":"2.0",
    "id":1,
    "result":[
        {
            "id":"1",
            "name":"API test",
            "is_active":"1",
            "token":"6bd0932fe7f4b5e6e4bc3c72800bfdef36a2c5de2f38f756dfb5bd632ebf",
            "last_modified":"1403392631"
        }
    ]
}
```

### Example with Python

Here a basic example written in Python to create a task:

```python
#!/usr/bin/env python

import requests
import json

def main():
    url = "http://demo.kanboard.net/jsonrpc.php"
    api_key = "be4271664ca8169d32af49d8e1ec854edb0290bc3588a2e356275eab9505"
    headers = {"content-type": "application/json"}

    payload = {
        "method": "createTask",
        "params": {
            "title": "Python API test",
            "project_id": 1
        },
        "jsonrpc": "2.0",
        "id": 1,
    }

    response = requests.post(
        url,
        data=json.dumps(payload),
        headers=headers,
        auth=("jsonrpc", api_key)
    )

    if response.status_code == 401:
        print "Authentication failed"
    else:
        result = response.json()

        assert result["result"] == True
        assert result["jsonrpc"]
        assert result["id"] == 1

        print "Task created successfully!"

if __name__ == "__main__":
    main()
```

Run this script from your terminal:

```bash
python jsonrpc.py
Task created successfully!
```

### Example with a PHP client:

I wrote a simple [Json-RPC Client/Server library in PHP](https://github.com/fguillot/JsonRPC), here an example:

```php
<?php

$client = new JsonRPC\Client('http://localhost:8000/jsonrpc.php');
$client->authentication('jsonrpc', '19ffd9709d03ce50675c3a43d1c49c1ac207f4bc45f06c5b2701fbdf8929');

print_r($client->getAllProjects());

```

The response:

```
Array
(
    [0] => Array
        (
            [id] => 1
            [name] => API test
            [is_active] => 1
            [token] => 6bd0932fe7f4b5e6e4bc3c72800bfdef36a2c5de2f38f756dfb5bd632ebf
            [last_modified] => 1403392631
        )

)
```

### Example with Ruby

This example can be used with Kanboard configured with Reverse-Proxy authentication and the API configured with a custom authentication header:

```ruby
require 'faraday'

conn = Faraday.new(:url => 'https://kanboard.example.com') do |faraday|
    faraday.response :logger
    faraday.headers['X-API-Auth'] = 'XXX'      # base64_encode('jsonrpc:API_KEY')
    faraday.basic_auth(ENV['user'], ENV['pw']) # user/pass to get through basic auth
    faraday.adapter Faraday.default_adapter    # make requests with Net::HTTP
end

response = conn.post do |req|
    req.url '/jsonrpc.php'
    req.headers['Content-Type'] = 'application/json'
    req.body = '{ "jsonrpc": "2.0", "id": 1, "method": "getAllProjects" }'
end

puts response.body
```


### Example with Java

This is a basic example using Spring. For proper usage see [this link](http://spring.io/guides/gs/consuming-rest).

```java
import java.io.UnsupportedEncodingException;
import java.util.Base64;

import org.springframework.http.HttpEntity;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.web.client.RestTemplate;

public class ProjectService {

    public void getAllProjects() throws UnsupportedEncodingException {

        RestTemplate restTemplate = new RestTemplate();

        String url = "http://localhost/kanboard/jsonrpc.php";
        String requestJson = "{\"jsonrpc\": \"2.0\", \"method\": \"getAllProjects\", \"id\": 1}";
        String user = "jsonrpc";
        String apiToken = "19ffd9709d03ce50675c3a43d1c49c1ac207f4bc45f06c5b2701fbdf8929";

        // encode api token
        byte[] xApiAuthTokenBytes = String.join(":", user, apiToken).getBytes("utf-8");
        String xApiAuthToken = Base64.getEncoder().encodeToString(xApiAuthTokenBytes);

        // consume request
        HttpHeaders headers = new HttpHeaders();
        headers.add("X-API-Auth", xApiAuthToken);
        headers.setContentType(MediaType.APPLICATION_JSON);
        HttpEntity<String> entity = new HttpEntity<String>(requestJson, headers);
        String answer = restTemplate.postForObject(url, entity, String.class);
        System.out.println(answer);
    }
}
```

Procedures
----------

### getVersion

- Purpose: **Get the application version**
- Parameters: none
- Result: **version** (Example: 1.0.12, master)

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getVersion",
    "id": 1661138292
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1661138292,
    "result": "1.0.13"
}
```

### getTimezone

- Purpose: **Get the application timezone**
- Parameters: none
- Result on success: **Timezone** (Example: UTC, Europe/Paris)
- Result on failure: **Default timezone** (UTC)

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getTimezone",
    "id": 1661138292
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1661138292,
    "result": "Europe\/Paris"
}
```

### getDefaultTaskColors

- Purpose: **Get all default task colors**
- Parameters: None
- Result on success: **Color properties**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getDefaultTaskColors",
    "id": 2108929212
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2108929212,
    "result": {
        "yellow": {
            "name": "Yellow",
            "background": "rgb(245, 247, 196)",
            "border": "rgb(223, 227, 45)"
        },
        "blue": {
            "name": "Blue",
            "background": "rgb(219, 235, 255)",
            "border": "rgb(168, 207, 255)"
        },
        "green": {
            "name": "Green",
            "background": "rgb(189, 244, 203)",
            "border": "rgb(74, 227, 113)"
        },
        "purple": {
            "name": "Purple",
            "background": "rgb(223, 176, 255)",
            "border": "rgb(205, 133, 254)"
        },
        "red": {
            "name": "Red",
            "background": "rgb(255, 187, 187)",
            "border": "rgb(255, 151, 151)"
        },
        "orange": {
            "name": "Orange",
            "background": "rgb(255, 215, 179)",
            "border": "rgb(255, 172, 98)"
        },
        "grey": {
            "name": "Grey",
            "background": "rgb(238, 238, 238)",
            "border": "rgb(204, 204, 204)"
        },
        "brown": {
            "name": "Brown",
            "background": "#d7ccc8",
            "border": "#4e342e"
        },
        "deep_orange": {
            "name": "Deep Orange",
            "background": "#ffab91",
            "border": "#e64a19"
        },
        "dark_grey": {
            "name": "Dark Grey",
            "background": "#cfd8dc",
            "border": "#455a64"
        },
        "pink": {
            "name": "Pink",
            "background": "#f48fb1",
            "border": "#d81b60"
        },
        "teal": {
            "name": "Teal",
            "background": "#80cbc4",
            "border": "#00695c"
        },
        "cyan": {
            "name": "Cyan",
            "background": "#b2ebf2",
            "border": "#00bcd4"
        },
        "lime": {
            "name": "Lime",
            "background": "#e6ee9c",
            "border": "#afb42b"
        },
        "light_green": {
            "name": "Light Green",
            "background": "#dcedc8",
            "border": "#689f38"
        },
        "amber": {
            "name": "Amber",
            "background": "#ffe082",
            "border": "#ffa000"
        }
    }
}
```

### getDefaultTaskColor

- Purpose: **Get default task color**
- Parameters: None
- Result on success: **color_id**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getDefaultTaskColor",
    "id": 1144775215
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1144775215,
    "result": "yellow"
}
```

### getColorList

- Purpose: **Get the list of task colors**
- Parameters: none
- Result on success: **Dictionary of color_id => color_name**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getColorList",
    "id": 1677051386
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1677051386,
    "result": {
        "yellow": "Yellow",
        "blue": "Blue",
        "green": "Green",
        "purple": "Purple",
        "red": "Red",
        "orange": "Orange",
        "grey": "Grey",
        "brown": "Brown",
        "deep_orange": "Deep Orange",
        "dark_grey": "Dark Grey",
        "pink": "Pink",
        "teal": "Teal",
        "cyan": "Cyan",
        "lime": "Lime",
        "light_green": "Light Green",
        "amber": "Amber"
    }
}
```

### createProject

- Purpose: **Create a new project**
- Parameters:
    - **name** (string, required)
    - **description** (string, optional)
- Result on success: **project_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createProject",
    "id": 1797076613,
    "params": {
        "name": "PHP client"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1797076613,
    "result": 2
}
```

### getProjectById

- Purpose: **Get project information**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **project properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectById",
    "id": 226760253,
    "params": {
        "project_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 226760253,
    "result": {
        "id": "1",
        "name": "API test",
        "is_active": "1",
        "token": "",
        "last_modified": "1436119135",
        "is_public": "0",
        "is_private": "0",
        "is_everybody_allowed": "0",
        "default_swimlane": "Default swimlane",
        "show_default_swimlane": "1",
        "description": "test",
        "identifier": "",
        "url": {
            "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",
            "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",
            "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"
        }
    }
}
```

### getProjectByName

- Purpose: **Get project information**
- Parameters:
    - **name** (string, required)
- Result on success: **project properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectByName",
    "id": 1620253806,
    "params": {
        "name": "Test"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1620253806,
    "result": {
        "id": "1",
        "name": "Test",
        "is_active": "1",
        "token": "",
        "last_modified": "1436119135",
        "is_public": "0",
        "is_private": "0",
        "is_everybody_allowed": "0",
        "default_swimlane": "Default swimlane",
        "show_default_swimlane": "1",
        "description": "test",
        "identifier": "",
        "url": {
            "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",
            "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",
            "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"
        }
    }
}
```

### getAllProjects

- Purpose: **Get all available projects**
- Parameters:
    - **none**
- Result on success: **List of projects**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllProjects",
    "id": 2134420212
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2134420212,
    "result": [
        {
            "id": "1",
            "name": "API test",
            "is_active": "1",
            "token": "",
            "last_modified": "1436119570",
            "is_public": "0",
            "is_private": "0",
            "is_everybody_allowed": "0",
            "default_swimlane": "Default swimlane",
            "show_default_swimlane": "1",
            "description": null,
            "identifier": "",
            "url": {
                "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",
                "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",
                "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"
            }
        }
    ]
}
```

### updateProject

- Purpose: **Update a project**
- Parameters:
    - **id** (integer, required)
    - **name** (string, required)
    - **description** (string, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateProject",
    "id": 1853996288,
    "params": {
        "id": 1,
        "name": "PHP client update"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1853996288,
    "result": true
}
```

### removeProject

- Purpose: **Remove a project**
- Parameters:
    **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeProject",
    "id": 46285125,
    "params": {
        "project_id": "2"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 46285125,
    "result": true
}
```

### enableProject

- Purpose: **Enable a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "enableProject",
    "id": 1775494839,
    "params": [
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1775494839,
    "result": true
}
```

### disableProject

- Purpose: **Disable a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "disableProject",
    "id": 1734202312,
    "params": [
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1734202312,
    "result": true
}
```

### enableProjectPublicAccess

- Purpose: **Enable public access for a given project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "enableProjectPublicAccess",
    "id": 103792571,
    "params": [
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 103792571,
    "result": true
}
```

### disableProjectPublicAccess

- Purpose: **Disable public access for a given project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "disableProjectPublicAccess",
    "id": 942472945,
    "params": [
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 942472945,
    "result": true
}
```

### getProjectActivity

- Purpose: **Get activity stream for a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **List of events**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectActivity",
    "id": 942472945,
    "params": [
        "project_id": 1
    ]
}
```

### getProjectActivities

- Purpose: **Get Activityfeed for Project(s)**
- Parameters:
    - **project_ids** (integer array, required)
- Result on success: **List of events**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectActivities",
    "id": 942472945,
    "params": [
        "project_ids": [1,2]
    ]
}
```

### getMembers

- Purpose: **Get members of a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: Key/value pair of user_id and username
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getMembers",
    "id": 1944388643,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1944388643,
    "result": {
        "1": "user1",
        "2": "user2",
        "3": "user3"
    }
}
```

### revokeUser

- Purpose: **Revoke user access for a given project**
- Parameters:
    - **project_id** (integer, required)
    - **user_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "revokeUser",
    "id": 251218350,
    "params": [
        1,
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 251218350,
    "result": true
}
```

### allowUser

- Purpose: **Grant user access for a given project**
- Parameters:
    - **project_id** (integer, required)
    - **user_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "allowUser",
    "id": 2111451404,
    "params": [
        1,
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2111451404,
    "result": true
}
```


### getBoard

- Purpose: **Get all necessary information to display a board**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **board properties**
- Result on failure: **empty list**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getBoard",
    "id": 827046470,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 827046470,
    "result": [
        {
            "id": 0,
            "name": "Default swimlane",
            "columns": [
                {
                    "id": "1",
                    "title": "Backlog",
                    "position": "1",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [],
                    "nb_tasks": 0,
                    "score": 0
                },
                {
                    "id": "2",
                    "title": "Ready",
                    "position": "2",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [
                        {
                            "nb_comments":"0",
                            "nb_files":"0",
                            "nb_subtasks":"0",
                            "nb_completed_subtasks":"0",
                            "nb_links":"0",
                            "id":"2",
                            "reference":"",
                            "title":"Test",
                            "description":"",
                            "date_creation":"1430870507",
                            "date_modification":"1430870507",
                            "date_completed":null,
                            "date_due":"0",
                            "color_id":"yellow",
                            "project_id":"1",
                            "column_id":"2",
                            "swimlane_id":"0",
                            "owner_id":"0",
                            "creator_id":"1",
                            "position":"1",
                            "is_active":"1",
                            "score":"0",
                            "category_id":"0",
                            "date_moved":"1430870507",
                            "recurrence_status":"0",
                            "recurrence_trigger":"0",
                            "recurrence_factor":"0",
                            "recurrence_timeframe":"0",
                            "recurrence_basedate":"0",
                            "recurrence_parent":null,
                            "recurrence_child":null,
                            "assignee_username":null,
                            "assignee_name":null
                        }
                    ],
                    "nb_tasks": 1,
                    "score": 0
                },
                {
                    "id": "3",
                    "title": "Work in progress",
                    "position": "3",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [
                        {
                            "nb_comments":"0",
                            "nb_files":"0",
                            "nb_subtasks":"1",
                            "nb_completed_subtasks":"0",
                            "nb_links":"0",
                            "id":"1",
                            "reference":"",
                            "title":"Task with comment",
                            "description":"",
                            "date_creation":"1430783188",
                            "date_modification":"1430783188",
                            "date_completed":null,
                            "date_due":"0",
                            "color_id":"red",
                            "project_id":"1",
                            "column_id":"3",
                            "swimlane_id":"0",
                            "owner_id":"1",
                            "creator_id":"0",
                            "position":"1",
                            "is_active":"1",
                            "score":"0",
                            "category_id":"0",
                            "date_moved":"1430783191",
                            "recurrence_status":"0",
                            "recurrence_trigger":"0",
                            "recurrence_factor":"0",
                            "recurrence_timeframe":"0",
                            "recurrence_basedate":"0",
                            "recurrence_parent":null,
                            "recurrence_child":null,
                            "assignee_username":"admin",
                            "assignee_name":null
                        }
                    ],
                    "nb_tasks": 1,
                    "score": 0
                },
                {
                    "id": "4",
                    "title": "Done",
                    "position": "4",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [],
                    "nb_tasks": 0,
                    "score": 0
                }
            ],
            "nb_columns": 4,
            "nb_tasks": 2
        }
    ]
}
```

### getColumns

- Purpose: **Get all columns information for a given project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **columns properties**
- Result on failure: **empty list**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getColumns",
    "id": 887036325,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 887036325,
    "result": [
        {
            "id": "1",
            "title": "Backlog",
            "position": "1",
            "project_id": "1",
            "task_limit": "0"
        },
        {
            "id": "2",
            "title": "Ready",
            "position": "2",
            "project_id": "1",
            "task_limit": "0"
        },
        {
            "id": "3",
            "title": "Work in progress",
            "position": "3",
            "project_id": "1",
            "task_limit": "0"
        }
    ]
}
```

### getColumn

- Purpose: **Get a single column**
- Parameters:
    - **column_id** (integer, required)
- Result on success: **column properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getColumn",
    "id": 1242049935,
    "params": [
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1242049935,
    "result": {
        "id": "2",
        "title": "Youpi",
        "position": "2",
        "project_id": "1",
        "task_limit": "5"
    }
}
```

### moveColumnUp

- Purpose: **Move up the column position**
- Parameters:
    - **project_id** (integer, required)
    - **column_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveColumnUp",
    "id": 99275573,
    "params": [
        1,
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 99275573,
    "result": true
}
```

### moveColumnDown

- Purpose: **Move down the column position**
- Parameters:
    - **project_id** (integer, required)
    - **column_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveColumnDown",
    "id": 957090649,
    "params": {
        "project_id": 1,
        "column_id": 2
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 957090649,
    "result": true
}
```

### updateColumn

- Purpose: **Update column properties**
- Parameters:
    - **column_id** (integer, required)
    - **title** (string, required)
    - **task_limit** (integer, optional)
    - **description** (string, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateColumn",
    "id": 480740641,
    "params": [
        2,
        "Boo",
        5
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 480740641,
    "result": true
}
```

### addColumn

- Purpose: **Add a new column**
- Parameters:
    - **project_id** (integer, required)
    - **title** (string, required)
    - **task_limit** (integer, optional)
    - **description** (string, optional)
- Result on success: **column_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "addColumn",
    "id": 638544704,
    "params": [
        1,
        "Boo"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 638544704,
    "result": 5
}
```

### removeColumn

- Purpose: **Remove a column**
- Parameters:
    - **column_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeColumn",
    "id": 1433237746,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": true
}
```

### getDefaultSwimlane

- Purpose: **Get the default swimlane for a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getDefaultSwimlane",
    "id": 898774713,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 898774713,
    "result": {
        "id": "1",
        "default_swimlane": "Default swimlane",
        "show_default_swimlane": "1"
    }
}
```

### getActiveSwimlanes

- Purpose: **Get the list of enabled swimlanes of a project (include default swimlane if enabled)**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **List of swimlanes**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getActiveSwimlanes",
    "id": 934789422,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 934789422,
    "result": [
        {
            "id": 0,
            "name": "Default swimlane"
        },
        {
            "id": "2",
            "name": "Swimlane A"
        }
    ]
}
```

### getAllSwimlanes

- Purpose: **Get the list of all swimlanes of a project (enabled or disabled) and sorted by position**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **List of swimlanes**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllSwimlanes",
    "id": 509791576,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 509791576,
    "result": [
        {
            "id": "1",
            "name": "Another swimlane",
            "position": "1",
            "is_active": "1",
            "project_id": "1"
        },
        {
            "id": "2",
            "name": "Swimlane A",
            "position": "2",
            "is_active": "1",
            "project_id": "1"
        }
    ]
}
```

### getSwimlane

- Purpose: **Get the a swimlane by id**
- Parameters:
    - **swimlane_id** (integer, required)
- Result on success: **swimlane properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getSwimlane",
    "id": 131071870,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 131071870,
    "result": {
        "id": "1",
        "name": "Swimlane 1",
        "position": "1",
        "is_active": "1",
        "project_id": "1"
    }
}
```

### getSwimlaneById

- Purpose: **Get the a swimlane by id**
- Parameters:
    - **swimlane_id** (integer, required)
- Result on success: **swimlane properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getSwimlaneById",
    "id": 131071870,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 131071870,
    "result": {
        "id": "1",
        "name": "Swimlane 1",
        "position": "1",
        "is_active": "1",
        "project_id": "1"
    }
}
```

### getSwimlaneByName

- Purpose: **Get the a swimlane by name**
- Parameters:
    - **project_id** (integer, required)
    - **name** (string, required)
- Result on success: **swimlane properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getSwimlaneByName",
    "id": 824623567,
    "params": [
        1,
        "Swimlane 1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 824623567,
    "result": {
        "id": "1",
        "name": "Swimlane 1",
        "position": "1",
        "is_active": "1",
        "project_id": "1"
    }
}
```

### moveSwimlaneUp

- Purpose: **Move up the swimlane position**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveSwimlaneUp",
    "id": 99275573,
    "params": [
        1,
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 99275573,
    "result": true
}
```

### moveSwimlaneDown

- Purpose: **Move down the swimlane position**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveSwimlaneDown",
    "id": 957090649,
    "params": {
        "project_id": 1,
        "swimlane_id": 2
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 957090649,
    "result": true
}
```

### updateSwimlane

- Purpose: **Update swimlane properties**
- Parameters:
    - **swimlane_id** (integer, required)
    - **name** (string, required)
    - **description** (string, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateSwimlane",
    "id": 87102426,
    "params": [
        "1",
        "Another swimlane"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 87102426,
    "result": true
}
```

### addSwimlane

- Purpose: **Add a new swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **name** (string, required)
    - **description** (string, optional)
- Result on success: **swimlane_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "addSwimlane",
    "id": 849940086,
    "params": [
        1,
        "Swimlane 1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 849940086,
    "result": 1
}
```

### removeSwimlane

- Purpose: **Remove a swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeSwimlane",
    "id": 1433237746,
    "params": [
        2,
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": true
}
```

### disableSwimlane

- Purpose: **Enable a swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "disableSwimlane",
    "id": 1433237746,
    "params": [
        2,
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": true
}
```

### enableSwimlane

- Purpose: **Enable a swimlane**
- Parameters:
    - **project_id** (integer, required)
    - **swimlane_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "enableSwimlane",
    "id": 1433237746,
    "params": [
        2,
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": true
}
```

### getAvailableActions

- Purpose: **Get list of available automatic actions**
- Parameters: none
- Result on success: **list of actions**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAvailableActions",
    "id": 1217735483
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1217735483,
    "result": {
        "TaskLogMoveAnotherColumn": "Add a comment logging moving the task between columns",
        "TaskAssignColorUser": "Assign a color to a specific user",
        "TaskAssignColorColumn": "Assign a color when the task is moved to a specific column",
        "TaskAssignCategoryColor": "Assign automatically a category based on a color",
        "TaskAssignColorCategory": "Assign automatically a color based on a category",
        "TaskAssignSpecificUser": "Assign the task to a specific user",
        "TaskAssignCurrentUser": "Assign the task to the person who does the action",
        "TaskUpdateStartDate": "Automatically update the start date",
        "TaskAssignUser": "Change the assignee based on an external username",
        "TaskAssignCategoryLabel": "Change the category based on an external label",
        "TaskClose": "Close a task",
        "CommentCreation": "Create a comment from an external provider",
        "TaskCreation": "Create a task from an external provider",
        "TaskDuplicateAnotherProject": "Duplicate the task to another project",
        "TaskMoveColumnAssigned": "Move the task to another column when assigned to a user",
        "TaskMoveColumnUnAssigned": "Move the task to another column when assignee is cleared",
        "TaskMoveAnotherProject": "Move the task to another project",
        "TaskOpen": "Open a task"
    }
}
```

### getAvailableActionEvents

- Purpose: **Get list of available events for actions**
- Parameters: none
- Result on success: **list of events**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAvailableActionEvents",
    "id": 2116665643
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2116665643,
    "result": {
        "bitbucket.webhook.commit": "Bitbucket commit received",
        "task.close": "Closing a task",
        "github.webhook.commit": "Github commit received",
        "github.webhook.issue.assignee": "Github issue assignee change",
        "github.webhook.issue.closed": "Github issue closed",
        "github.webhook.issue.commented": "Github issue comment created",
        "github.webhook.issue.label": "Github issue label change",
        "github.webhook.issue.opened": "Github issue opened",
        "github.webhook.issue.reopened": "Github issue reopened",
        "gitlab.webhook.commit": "Gitlab commit received",
        "gitlab.webhook.issue.closed": "Gitlab issue closed",
        "gitlab.webhook.issue.opened": "Gitlab issue opened",
        "task.move.column": "Move a task to another column",
        "task.open": "Open a closed task",
        "task.assignee_change": "Task assignee change",
        "task.create": "Task creation",
        "task.create_update": "Task creation or modification",
        "task.update": "Task modification"
    }
}
```

### getCompatibleActionEvents

- Purpose: **Get list of events compatible with an action**
- Parameters:
    - **action_name** (string, required)
- Result on success: **list of events**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getCompatibleActionEvents",
    "id": 899370297,
    "params": [
        "TaskClose"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 899370297,
    "result": {
        "bitbucket.webhook.commit": "Bitbucket commit received",
        "github.webhook.commit": "Github commit received",
        "github.webhook.issue.closed": "Github issue closed",
        "gitlab.webhook.commit": "Gitlab commit received",
        "gitlab.webhook.issue.closed": "Gitlab issue closed",
        "task.move.column": "Move a task to another column"
    }
}
```

### getActions

- Purpose: **Get list of actions for a project**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **list of actions properties**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getActions",
    "id": 1433237746,
    "params": [
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": [
        {
            "id" : "13",
            "project_id" : "2",
            "event_name" : "task.move.column",
            "action_name" : "TaskAssignSpecificUser",
            "params" : {
                "column_id" : "5",
                "user_id" : "1"
            }
        }
    ]
}
```

### createAction

- Purpose: **Create an action**
- Parameters:
    - **project_id** (integer, required)
    - **event_name** (string, required)
    - **action_name** (string, required)
    - **params** (key/value parameters, required)
- Result on success: **action_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createAction",
    "id": 1433237746,
    "params": {
        "project_id" : "2",
        "event_name" : "task.move.column",
        "action_name" : "TaskAssignSpecificUser",
        "params" : {
            "column_id" : "3",
            "user_id" : "2"
        }
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1433237746,
    "result": 14
}
```

### removeAction

- Purpose: **Remove an action**
- Parameters:
    - **action_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeAction",
    "id": 1510741671,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1510741671,
    "result": true
}
```

### createTask

- Purpose: **Create a new task**
- Parameters:
    - **title** (string, required)
    - **project_id** (integer, required)
    - **color_id** (string, optional)
    - **column_id** (integer, optional)
    - **owner_id** (integer, optional)
    - **creator_id** (integer, optional)
    - **date_due**: ISO8601 format (string, optional)
    - **description** Markdown content (string, optional)
    - **category_id** (integer, optional)
    - **score** (integer, optional)
    - **swimlane_id** (integer, optional)
    - **recurrence_status**  (integer, optional)
    - **recurrence_trigger**  (integer, optional)
    - **recurrence_factor**  (integer, optional)
    - **recurrence_timeframe**  (integer, optional)
    - **recurrence_basedate**  (integer, optional)
- Result on success: **task_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createTask",
    "id": 1176509098,
    "params": {
        "owner_id": 1,
        "creator_id": 0,
        "date_due": "",
        "description": "",
        "category_id": 0,
        "score": 0,
        "title": "Test",
        "project_id": 1,
        "color_id": "green",
        "column_id": 2,
        "recurrence_status": 0,
        "recurrence_trigger": 0,
        "recurrence_factor": 0,
        "recurrence_timeframe": 0,
        "recurrence_basedate": 0
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1176509098,
    "result": 3
}
```

### getTask

- Purpose: **Get task by the unique id**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **task properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getTask",
    "id": 700738119,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 700738119,
    "result": {
        "id": "1",
        "title": "Task #1",
        "description": "",
        "date_creation": "1409963206",
        "color_id": "blue",
        "project_id": "1",
        "column_id": "2",
        "owner_id": "1",
        "position": "1",
        "is_active": "1",
        "date_completed": null,
        "score": "0",
        "date_due": "0",
        "category_id": "0",
        "creator_id": "0",
        "date_modification": "1409963206",
        "reference": "",
        "date_started": null,
        "time_spent": "0",
        "time_estimated": "0",
        "swimlane_id": "0",
        "date_moved": "1430875287",
        "recurrence_status": "0",
        "recurrence_trigger": "0",
        "recurrence_factor": "0",
        "recurrence_timeframe": "0",
        "recurrence_basedate": "0",
        "recurrence_parent": null,
        "recurrence_child": null,
        "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=1&project_id=1",
        "color": {
            "name": "Yellow",
            "background": "rgb(245, 247, 196)",
            "border": "rgb(223, 227, 45)"
        }
    }
}
```

### getTaskByReference

- Purpose: **Get task by the external reference**
- Parameters:
    - **project_id** (integer, required)
    - **reference** (string, required)
- Result on success: **task properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getTaskByReference",
    "id": 1992081213,
    "params": {
        "project_id": 1,
        "reference": "TICKET-1234"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1992081213,
    "result": {
        "id": "5",
        "title": "Task with external ticket number",
        "description": "[Link to my ticket](http:\/\/my-ticketing-system\/1234)",
        "date_creation": "1434227446",
        "color_id": "yellow",
        "project_id": "1",
        "column_id": "1",
        "owner_id": "0",
        "position": "4",
        "is_active": "1",
        "date_completed": null,
        "score": "0",
        "date_due": "0",
        "category_id": "0",
        "creator_id": "0",
        "date_modification": "1434227446",
        "reference": "TICKET-1234",
        "date_started": null,
        "time_spent": "0",
        "time_estimated": "0",
        "swimlane_id": "0",
        "date_moved": "1434227446",
        "recurrence_status": "0",
        "recurrence_trigger": "0",
        "recurrence_factor": "0",
        "recurrence_timeframe": "0",
        "recurrence_basedate": "0",
        "recurrence_parent": null,
        "recurrence_child": null,
        "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=5&project_id=1"
    }
}
```

### getAllTasks

- Purpose: **Get all available tasks**
- Parameters:
    - **project_id** (integer, required)
    - **status_id**: The value 1 for active tasks and 0 for inactive (integer, required)
- Result on success: **List of tasks**
- Result on failure: **false**

Request example to fetch all tasks on the board:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllTasks",
    "id": 133280317,
    "params": {
        "project_id": 1,
        "status_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "id": "1",
            "title": "Task #1",
            "description": "",
            "date_creation": "1409961789",
            "color_id": "blue",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "position": "1",
            "is_active": "1",
            "date_completed": null,
            "score": "0",
            "date_due": "0",
            "category_id": "0",
            "creator_id": "0",
            "date_modification": "1409961789",
            "reference": "",
            "date_started": null,
            "time_spent": "0",
            "time_estimated": "0",
            "swimlane_id": "0",
            "date_moved": "1430783191",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=1&project_id=1"
        },
        {
            "id": "2",
            "title": "Test",
            "description": "",
            "date_creation": "1409962115",
            "color_id": "green",
            "project_id": "1",
            "column_id": "2",
            "owner_id": "1",
            "position": "2",
            "is_active": "1",
            "date_completed": null,
            "score": "0",
            "date_due": "0",
            "category_id": "0",
            "creator_id": "0",
            "date_modification": "1409962115",
            "reference": "",
            "date_started": null,
            "time_spent": "0",
            "time_estimated": "0",
            "swimlane_id": "0",
            "date_moved": "1430783191",
            "recurrence_status": "0",
            "recurrence_trigger": "0",
            "recurrence_factor": "0",
            "recurrence_timeframe": "0",
            "recurrence_basedate": "0",
            "recurrence_parent": null,
            "recurrence_child": null,
            "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=2&project_id=1"
        },
        ...
    ]
}
```

### getOverdueTasks

- Purpose: **Get all overdue tasks**
- Result on success: **List of tasks**
- Result on failure: **false**

Request example to fetch all tasks on the board:

```json
{
    "jsonrpc": "2.0",
    "method": "getOverdueTasks",
    "id": 133280317
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "id": "1",
            "title": "Task #1",
            "date_due": "1409961789",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        {
            "id": "2",
            "title": "Test",
            "date_due": "1409962115",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        ...
    ]
}
```

### getOverdueTasksByProject

- Purpose: **Get all overdue tasks for a special project**
- Result on success: **List of tasks**
- Result on failure: **false**

Request example to fetch all tasks on the board:

```json
{
    "jsonrpc": "2.0",
    "method": "getOverdueTasksByProject",
    "id": 133280317,
    "params": {
        "project_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "id": "1",
            "title": "Task #1",
            "date_due": "1409961789",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        {
            "id": "2",
            "title": "Test",
            "date_due": "1409962115",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        ...
    ]
}
```

### updateTask

- Purpose: **Update a task**
- Parameters:
    - **id** (integer, required)
    - **title** (string, optional)
    - **project_id** (integer, optional)
    - **color_id** (string, optional)
    - **owner_id** (integer, optional)
    - **creator_id** (integer, optional)
    - **date_due**: ISO8601 format (string, optional)
    - **description** Markdown content (string, optional)
    - **category_id** (integer, optional)
    - **score** (integer, optional)
    - **recurrence_status**  (integer, optional)
    - **recurrence_trigger**  (integer, optional)
    - **recurrence_factor**  (integer, optional)
    - **recurrence_timeframe**  (integer, optional)
    - **recurrence_basedate**  (integer, optional)
- Result on success: **true**
- Result on failure: **false**

Request example to change the task color:

```json
{
    "jsonrpc": "2.0",
    "method": "updateTask",
    "id": 1406803059,
    "params": {
        "id": 1,
        "color_id": "blue"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1406803059,
    "result": true
}
```

### openTask

- Purpose: **Set a task to the status open**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "openTask",
    "id": 1888531925,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1888531925,
    "result": true
}
```

### closeTask

- Purpose: **Set a task to the status close**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "closeTask",
    "id": 1654396960,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1654396960,
    "result": true
}
```

### removeTask

- Purpose: **Remove a task**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeTask",
    "id": 1423501287,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1423501287,
    "result": true
}
```

### moveTaskPosition

- Purpose: **Move a task to another column or another position**
- Parameters:
    - **project_id** (integer, required)
    - **task_id** (integer, required)
    - **column_id** (integer, required)
    - **position** (integer, required)
    - **swimlane_id** (integer, optional, default=0)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "moveTaskPosition",
    "id": 117211800,
    "params": {
        "project_id": 1,
        "task_id": 1,
        "column_id": 2,
        "position": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 117211800,
    "result": true
}
```

### createUser

- Purpose: **Create a new user**
- Parameters:
    - **username** Must be unique (string, required)
    - **password** Must have at least 6 characters (string, required)
    - **name** (string, optional)
    - **email** (string, optional)
    - **is_admin** Set the value 1 for admins or 0 for regular users (integer, optional)
    - **is_project_admin** Set the value 1 for project admins or 0 for regular users (integer, optional)
- Result on success: **user_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createUser",
    "id": 1518863034,
    "params": {
        "username": "biloute",
        "password": "123456"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1518863034,
    "result": 22
}
```

### createLdapUser

- Purpose: **Create a new user authentified by LDAP**
- Parameters:
    - **username** (string, optional if email is set)
    - **email** (string, optional if username is set)
    - **is_admin** Set the value 1 for admins or 0 for regular users (integer, optional)
    - **is_project_admin** Set the value 1 for project admins or 0 for regular users (integer, optional)
- Result on success: **user_id**
- Result on failure: **false**

The user will only be created if a matching is found on the LDAP server.
Username or email (or both) must be provided.

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createLdapUser",
    "id": 1518863034,
    "params": {
        "username": "biloute",
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1518863034,
    "result": 22
}
```

### getUser

- Purpose: **Get user information**
- Parameters:
    - **user_id** (integer, required)
- Result on success: **user properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getUser",
    "id": 1769674781,
    "params": {
        "user_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1769674781,
    "result": {
        "id": "1",
        "username": "biloute",
        "password": "$2y$10$dRs6pPoBu935RpmsrhmbjevJH5MgZ7Kr9QrnVINwwyZ3.MOwqg.0m",
        "is_admin": "0",
        "is_ldap_user": "0",
        "name": "",
        "email": "",
        "google_id": null,
        "github_id": null,
        "notifications_enabled": "0"
    }
}
```

### getAllUsers

- Purpose: **Get all available users**
- Parameters:
    - **none**
- Result on success: **List of users**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllUsers",
    "id": 1438712131
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1438712131,
    "result": [
        {
            "id": "1",
            "username": "biloute",
            "name": "",
            "email": "",
            "is_admin": "0",
            "is_ldap_user": "0",
            "notifications_enabled": "0",
            "google_id": null,
            "github_id": null
        },
        ...
    ]
}
```

### updateUser

- Purpose: **Update a user**
- Parameters:
    - **id** (integer)
    - **username** (string, optional)
    - **name** (string, optional)
    - **email** (string, optional)
    - **is_admin** (integer, optional)
    - **is_project_admin** Set the value 1 for project admins or 0 for regular users (integer, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateUser",
    "id": 322123657,
    "params": {
        "id": 1,
        "is_admin": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 322123657,
    "result": true
}
```

### removeUser

- Purpose: **Remove a user**
- Parameters:
    - **user_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeUser",
    "id": 2094191872,
    "params": {
        "user_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2094191872,
    "result": true
}
```


### createCategory

- Purpose: **Create a new category**
- Parameters:
- **project_id** (integer, required)
    - **name** (string, required, must be unique for the given project)
- Result on success: **category_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createCategory",
    "id": 541909890,
    "params": {
        "name": "Super category",
        "project_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 541909890,
    "result": 4
}
```

### getCategory

- Purpose: **Get category information**
- Parameters:
    - **category_id** (integer, required)
- Result on success: **category properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getCategory",
    "id": 203539163,
    "params": {
        "category_id": 1
    }
}
```

Response example:

```json
{

    "jsonrpc": "2.0",
    "id": 203539163,
    "result": {
        "id": "1",
        "name": "Super category",
        "project_id": "1"
    }
}
```

### getAllCategories

- Purpose: **Get all available categories**
- Parameters:
    - **project_id** (integer, required)
- Result on success: **List of categories**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllCategories",
    "id": 1261777968,
    "params": {
        "project_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1261777968,
    "result": [
        {
            "id": "1",
            "name": "Super category",
            "project_id": "1"
        }
    ]
}
```

### updateCategory

- Purpose: **Update a category**
- Parameters:
    - **id** (integer, required)
    - **name** (string, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateCategory",
    "id": 570195391,
    "params": {
        "id": 1,
        "name": "Renamed category"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 570195391,
    "result": true
}
```

### removeCategory

- Purpose: **Remove a category**
- Parameters:
    - **category_id** (integer)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeCategory",
    "id": 88225706,
    "params": {
        "category_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 88225706,
    "result": true
}
```


### createComment

- Purpose: **Create a new comment**
- Parameters:
    - **task_id** (integer, required)
    - **user_id** (integer, required)
    - **content** Markdown content (string, required)
- Result on success: **comment_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createComment",
    "id": 1580417921,
    "params": {
        "task_id": 1,
        "user_id": 1,
        "content": "Comment #1"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1580417921,
    "result": 11
}
```

### getComment

- Purpose: **Get comment information**
- Parameters:
    - **comment_id** (integer, required)
- Result on success: **comment properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getComment",
    "id": 867839500,
    "params": {
        "comment_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 867839500,
    "result": {
        "id": "1",
        "task_id": "1",
        "user_id": "1",
        "date_creation": "1410881970",
        "comment": "Comment #1",
        "username": "admin",
        "name": null
    }
}
```

### getAllComments

- Purpose: **Get all available comments**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **List of comments**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllComments",
    "id": 148484683,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 148484683,
    "result": [
        {
            "id": "1",
            "date_creation": "1410882272",
            "task_id": "1",
            "user_id": "1",
            "comment": "Comment #1",
            "username": "admin",
            "name": null
        },
        ...
    ]
}
```

### updateComment

- Purpose: **Update a comment**
- Parameters:
    - **id** (integer, required)
    - **content** Markdown content (string, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateComment",
    "id": 496470023,
    "params": {
        "id": 1,
        "content": "Comment #1 updated"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1493368950,
    "result": true
}
```

### removeComment

- Purpose: **Remove a comment**
- Parameters:
    - **comment_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeComment",
    "id": 328836871,
    "params": {
        "comment_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 328836871,
    "result": true
}
```

### createSubtask

- Purpose: **Create a new subtask**
- Parameters:
    - **task_id** (integer, required)
    - **title** (integer, required)
    - **user_id** (int, optional)
    - **time_estimated** (int, optional)
    - **time_spent** (int, optional)
    - **status** (int, optional)
- Result on success: **subtask_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createSubtask",
    "id": 2041554661,
    "params": {
        "task_id": 1,
        "title": "Subtask #1"
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2041554661,
    "result": 45
}
```

### getSubtask

- Purpose: **Get subtask information**
- Parameters:
    - **subtask_id** (integer)
- Result on success: **subtask properties**
- Result on failure: **null**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getSubtask",
    "id": 133184525,
    "params": {
        "subtask_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 133184525,
    "result": {
        "id": "1",
        "title": "Subtask #1",
        "status": "0",
        "time_estimated": "0",
        "time_spent": "0",
        "task_id": "1",
        "user_id": "0"
    }
}
```

### getAllSubtasks

- Purpose: **Get all available subtasks**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **List of subtasks**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllSubtasks",
    "id": 2087700490,
    "params": {
        "task_id": 1
    }
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2087700490,
    "result": [
        {
            "id": "1",
            "title": "Subtask #1",
            "status": "0",
            "time_estimated": "0",
            "time_spent": "0",
            "task_id": "1",
            "user_id": "0",
            "username": null,
            "name": null,
            "status_name": "Todo"
        },
        ...
    ]
}
```

### updateSubtask

- Purpose: **Update a subtask**
- Parameters:
    - **id** (integer, required)
    - **task_id** (integer, required)
    - **title** (integer, optional)
    - **user_id** (integer, optional)
    - **time_estimated** (integer, optional)
    - **time_spent** (integer, optional)
    - **status** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateSubtask",
    "id": 191749979,
    "params": {
        "id": 1,
        "task_id": 1,
        "status": 1,
        "time_spent": 5,
        "user_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 191749979,
    "result": true
}
```

### removeSubtask

- Purpose: **Remove a subtask**
- Parameters:
    - **subtask_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeSubtask",
    "id": 1382487306,
    "params": {
        "subtask_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1382487306,
    "result": true
}
```

### getAllLinks

- Purpose: **Get the list of possible relations between tasks**
- Parameters: none
- Result on success: **List of links**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllLinks",
    "id": 113057196
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 113057196,
    "result": [
        {
            "id": "1",
            "label": "relates to",
            "opposite_id": "0"
        },
        {
            "id": "2",
            "label": "blocks",
            "opposite_id": "3"
        },
        {
            "id": "3",
            "label": "is blocked by",
            "opposite_id": "2"
        },
        {
            "id": "4",
            "label": "duplicates",
            "opposite_id": "5"
        },
        {
            "id": "5",
            "label": "is duplicated by",
            "opposite_id": "4"
        },
        {
            "id": "6",
            "label": "is a child of",
            "opposite_id": "7"
        },
        {
            "id": "7",
            "label": "is a parent of",
            "opposite_id": "6"
        },
        {
            "id": "8",
            "label": "targets milestone",
            "opposite_id": "9"
        },
        {
            "id": "9",
            "label": "is a milestone of",
            "opposite_id": "8"
        },
        {
            "id": "10",
            "label": "fixes",
            "opposite_id": "11"
        },
        {
            "id": "11",
            "label": "is fixed by",
            "opposite_id": "10"
        }
    ]
}
```

### getOppositeLinkId

- Purpose: **Get the opposite link id of a task link**
- Parameters:
    - **link_id** (integer, required)
- Result on success: **link_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getOppositeLinkId",
    "id": 407062448,
    "params": [
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 407062448,
    "result": "3"
}
```

### getLinkByLabel

- Purpose: **Get a link by label**
- Parameters:
    - **label** (integer, required)
- Result on success: **link properties**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getLinkByLabel",
    "id": 1796123316,
    "params": [
        "blocks"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1796123316,
    "result": {
        "id": "2",
        "label": "blocks",
        "opposite_id": "3"
    }
}
```

### getLinkById

- Purpose: **Get a link by id**
- Parameters:
    - **link_id** (integer, required)
- Result on success: **link properties**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getLinkById",
    "id": 1190238402,
    "params": [
        4
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1190238402,
    "result": {
        "id": "4",
        "label": "duplicates",
        "opposite_id": "5"
    }
}
```

### createLink

- Purpose: **Create a new task relation**
- Parameters:
    - **label** (integer, required)
    - **opposite_label** (integer, optional)
- Result on success: **link_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createLink",
    "id": 1040237496,
    "params": [
        "foo",
        "bar"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1040237496,
    "result": 13
}
```

### updateLink

- Purpose: **Update a link**
- Parameters:
    - **link_id** (integer, required)
    - **opposite_link_id** (integer, required)
    - **label** (string, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateLink",
    "id": 2110446926,
    "params": [
        "14",
        "12",
        "boo"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2110446926,
    "result": true
}
```

### removeLink

- Purpose: **Remove a link**
- Parameters:
    - **link_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeLink",
    "id": 2136522739,
    "params": [
        "14"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2136522739,
    "result": true
}
```

### createTaskLink

- Purpose: **Create a link between two tasks**
- Parameters:
    - **task_id** (integer, required)
    - **opposite_task_id** (integer, required)
    - **link_id** (integer, required)
- Result on success: **task_link_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createTaskLink",
    "id": 509742912,
    "params": [
        2,
        3,
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 509742912,
    "result": 1
}
```

### updateTaskLink

- Purpose: **Update task link**
- Parameters:
    - **task_link_id** (integer, required)
    - **task_id** (integer, required)
    - **opposite_task_id** (integer, required)
    - **link_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateTaskLink",
    "id": 669037109,
    "params": [
        1,
        2,
        4,
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 669037109,
    "result": true
}
```

### getTaskLinkById

- Purpose: **Get a task link**
- Parameters:
    - **task_link_id** (integer, required)
- Result on success: **task link properties**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getTaskLinkById",
    "id": 809885202,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 809885202,
    "result": {
        "id": "1",
        "link_id": "1",
        "task_id": "2",
        "opposite_task_id": "3"
    }
}
```

### getAllTaskLinks

- Purpose: **Get all links related to a task**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **list of task link**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllTaskLinks",
    "id": 810848359,
    "params": [
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 810848359,
    "result": [
        {
            "id": "1",
            "task_id": "3",
            "label": "relates to",
            "title": "B",
            "is_active": "1",
            "project_id": "1",
            "task_time_spent": "0",
            "task_time_estimated": "0",
            "task_assignee_id": "0",
            "task_assignee_username": null,
            "task_assignee_name": null,
            "column_title": "Backlog"
        }
    ]
}
```

### removeTaskLink

- Purpose: **Remove a link between two tasks**
- Parameters:
    - **task_link_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeTaskLink",
    "id": 473028226,
    "params": [
        1
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 473028226,
    "result": true
}
```

### createFile

- Purpose: **Create and upload a new task attachment**
- Parameters:
    - **project_id** (integer, required)
    - **task_id** (integer, required)
    - **filename** (integer, required)
    - **blob** File content encoded in base64 (string, required)
- Result on success: **file_id**
- Result on failure: **false**
- Note: **The maximum file size depends of your PHP configuration, this method should not be used to upload large files**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createFile",
    "id": 94500810,
    "params": [
        1,
        1,
        "My file",
        "cGxhaW4gdGV4dCBmaWxl"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 94500810,
    "result": 1
}
```

### getAllFiles

- Purpose: **Get all files attached to  task**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **list of files**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllFiles",
    "id": 1880662820,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1880662820,
    "result": [
        {
            "id": "1",
            "name": "My file",
            "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",
            "is_image": "0",
            "task_id": "1",
            "date": "1432509941",
            "user_id": "0",
            "size": "15",
            "username": null,
            "user_name": null
        }
    ]
}
```

### getFile

- Purpose: **Get file information**
- Parameters:
    - **file_id** (integer, required)
- Result on success: **file properties**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getFile",
    "id": 318676852,
    "params": [
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 318676852,
    "result": {
        "id": "1",
        "name": "My file",
        "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",
        "is_image": "0",
        "task_id": "1",
        "date": "1432509941",
        "user_id": "0",
        "size": "15"
    }
}
```

### downloadFile

- Purpose: **Download file contents (encoded in base64)**
- Parameters:
    - **file_id** (integer, required)
- Result on success: **base64 encoded string**
- Result on failure: **empty string**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "downloadFile",
    "id": 235943344,
    "params": [
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 235943344,
    "result": "cGxhaW4gdGV4dCBmaWxl"
}
```

### removeFile

- Purpose: **Remove file**
- Parameters:
    - **file_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeFile",
    "id": 447036524,
    "params": [
        "1"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 447036524,
    "result": true
}
```

### removeAllFiles

- Purpose: **Remove all files associated to a task**
- Parameters:
    - **task_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeAllFiles",
    "id": 593312993,
    "params": {
        "task_id": 1
    }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 593312993,
    "result": true
}
```

### getMe

- Purpose: **Get logged user session**
- Parameters: None
- Result on success: **user session data**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getMe",
    "id": 1718627783
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1718627783,
    "result": {
        "id": 2,
        "username": "user",
        "is_admin": false,
        "is_ldap_user": false,
        "name": "",
        "email": "",
        "google_id": null,
        "github_id": null,
        "notifications_enabled": "0",
        "timezone": null,
        "language": null,
        "disable_login_form": "0",
        "twofactor_activated": false,
        "twofactor_secret": null,
        "token": "",
        "notifications_filter": "4"
    }
}
```

### getMyDashboard

- Purpose: **Get the dashboard of the logged user without pagination**
- Parameters: None
- Result on success: **Dashboard information**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getMyDashboard",
    "id": 447898718
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1563664593,
    "result": {
        "projects": [
            {
                "id": "2",
                "name": "my project",
                "is_active": "1",
                "token": "",
                "last_modified": "1438205337",
                "is_public": "0",
                "is_private": "1",
                "is_everybody_allowed": "0",
                "default_swimlane": "Default swimlane",
                "show_default_swimlane": "1",
                "description": null,
                "identifier": "",
                "columns": [
                    {
                        "id": "5",
                        "title": "Backlog",
                        "position": "1",
                        "project_id": "2",
                        "task_limit": "0",
                        "description": "",
                        "nb_tasks": 0
                    },
                    {
                        "id": "6",
                        "title": "Ready",
                        "position": "2",
                        "project_id": "2",
                        "task_limit": "0",
                        "description": "",
                        "nb_tasks": 0
                    },
                    {
                        "id": "7",
                        "title": "Work in progress",
                        "position": "3",
                        "project_id": "2",
                        "task_limit": "0",
                        "description": "",
                        "nb_tasks": 0
                    },
                    {
                        "id": "8",
                        "title": "Done",
                        "position": "4",
                        "project_id": "2",
                        "task_limit": "0",
                        "description": "",
                        "nb_tasks": 0
                    }
                ],
                "url": {
                    "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=2",
                    "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=2",
                    "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=2"
                }
            }
        ],
        "tasks": [
            {
                "id": "1",
                "title": "new title",
                "date_due": "0",
                "date_creation": "1438205336",
                "project_id": "2",
                "color_id": "yellow",
                "time_spent": "0",
                "time_estimated": "0",
                "project_name": "my project",
                "url": "http:\/\/127.0.0.1:8000\/?controller=task&action=show&task_id=1&project_id=2"
            }
        ],
        "subtasks": []
    }
}
```

### getMyActivityStream

- Purpose: **Get the last 100 events for the logged user**
- Parameters: None
- Result on success: **List of events**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getMyActivityStream",
    "id": 1132562181
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1132562181,
    "result": [
        {
            "id": "1",
            "date_creation": "1438205054",
            "event_name": "task.create",
            "creator_id": "2",
            "project_id": "2",
            "task_id": "1",
            "author_username": "user",
            "author_name": "",
            "email": "",
            "task": {
                "id": "1",
                "reference": "",
                "title": "my user title",
                "description": "",
                "date_creation": "1438205054",
                "date_completed": null,
                "date_modification": "1438205054",
                "date_due": "0",
                "date_started": null,
                "time_estimated": "0",
                "time_spent": "0",
                "color_id": "yellow",
                "project_id": "2",
                "column_id": "5",
                "owner_id": "0",
                "creator_id": "2",
                "position": "1",
                "is_active": "1",
                "score": "0",
                "category_id": "0",
                "swimlane_id": "0",
                "date_moved": "1438205054",
                "recurrence_status": "0",
                "recurrence_trigger": "0",
                "recurrence_factor": "0",
                "recurrence_timeframe": "0",
                "recurrence_basedate": "0",
                "recurrence_parent": null,
                "recurrence_child": null,
                "category_name": null,
                "swimlane_name": null,
                "project_name": "my project",
                "default_swimlane": "Default swimlane",
                "column_title": "Backlog",
                "assignee_username": null,
                "assignee_name": null,
                "creator_username": "user",
                "creator_name": ""
            },
            "changes": [],
            "author": "user",
            "event_title": "user created the task #1",
            "event_content": "\n<p class=\"activity-title\">\n    user created the task <a href=\"\/?controller=task&amp;action=show&amp;task_id=1&amp;project_id=2\" class=\"\" title=\"\" >#1<\/a><\/p>\n<p class=\"activity-description\">\n    <em>my user title<\/em>\n<\/p>"
        }
    ]
}
```

### createMyPrivateProject

- Purpose: **Create a private project for the logged user**
- Parameters:
    - **name** (string, required)
    - **description** (string, optional)
- Result on success: **project_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createMyPrivateProject",
    "id": 1271580569,
    "params": [
        "my project"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1271580569,
    "result": 2
}
```

### getMyProjectsList

- Purpose: **Get projects of the connected user**
- Parameters: None
- Result on success: **dictionary of project_id => project_name**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getMyProjectsList",
    "id": 987834805
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 987834805,
    "result": {
        "2": "my project"
    }
}
```
### getMyOverdueTasks

- Purpose: **Get my overdue tasks**
- Result on success: **List of tasks**
- Result on failure: **false**

Request example to fetch all tasks on the board:

```json
{
    "jsonrpc": "2.0",
    "method": "getMyOverdueTasks",
    "id": 133280317
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 133280317,
    "result": [
        {
            "id": "1",
            "title": "Task #1",
            "date_due": "1409961789",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        {
            "id": "2",
            "title": "Test",
            "date_due": "1409962115",
            "project_id": "1",
            "project_name": "Test",
            "assignee_username":"admin",
            "assignee_name": null
        },
        ...
    ]
}
```

### getMyProjects

- Purpose: **Get projects of connected user with full details**
- Parameters:
    - **none**
- Result on success: **List of projects with details**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getmyProjects",
    "id": 2134420212
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2134420212,
    "result": [
        {
            "id": "1",
            "name": "API test",
            "is_active": "1",
            "token": "",
            "last_modified": "1436119570",
            "is_public": "0",
            "is_private": "0",
            "is_everybody_allowed": "0",
            "default_swimlane": "Default swimlane",
            "show_default_swimlane": "1",
            "description": null,
            "identifier": "",
            "url": {
                "board": "http:\/\/127.0.0.1:8000\/?controller=board&action=show&project_id=1",
                "calendar": "http:\/\/127.0.0.1:8000\/?controller=calendar&action=show&project_id=1",
                "list": "http:\/\/127.0.0.1:8000\/?controller=listing&action=show&project_id=1"
            }
        }
    ]
}
```

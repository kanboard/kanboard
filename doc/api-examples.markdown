API Examples
============

Example with cURL
-----------------

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

Example with Python
-------------------

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

Example with a PHP client
-------------------------

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

Example with Ruby
-----------------

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


Example with Java
-----------------

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

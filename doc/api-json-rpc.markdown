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

Usage
-----

- [Authentication](api-authentication.markdown)
- [Examples](api-examples.markdown)
- [Application Procedures](api-application-procedures.markdown)
- [Project Procedures](api-project-procedures.markdown)
- [Board Procedures](api-board-procedures.markdown)
- [Swimlane Procedures](api-swimlane-procedures.markdown)
- [Category Procedures](api-category-procedures.markdown)
- [Automatic Action Procedures](api-action-procedures.markdown)
- [Task Procedures](api-task-procedures.markdown)
- [Subtask Procedures](api-subtask-procedures.markdown)
- [File Procedures](api-file-procedures.markdown)
- [Link Procedures](api-link-procedures.markdown)
- [Comment Procedures](api-comment-procedures.markdown)
- [User Procedures](api-user-procedures.markdown)
- [User API Access Procedures](api-me-procedures.markdown)

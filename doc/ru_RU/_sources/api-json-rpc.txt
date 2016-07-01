Json-RPC API
============

User and application API
------------------------

There are two types of API access:

Application API
~~~~~~~~~~~~~~~

-  Access to the API with the user "jsonrpc" and the token available in
   settings
-  Access to all procedures
-  No permission checked
-  There is no user session on the server
-  Example of possible clients: tools to migrate/import data, create
   tasks from another system, etc...

User API
~~~~~~~~

-  Access to the API with the user credentials (username and password)
-  Access to a restricted set of procedures
-  The project permissions are checked
-  A user session is created on the server
-  Example of possible clients: mobile/desktop application, command line
   utility, etc...

Security
--------

-  Always use HTTPS with a valid certificate
-  If you make a mobile application, it's your job to store securely the
   user credentials on the device
-  After 3 authentication failure on the user api, the end-user have to
   unlock his account by using the login form
-  Two factor authentication is not yet available through the API

Protocol
--------

Kanboard use the protocol Json-RPC to interact with external programs.

JSON-RPC is a remote procedure call protocol encoded in JSON. Almost the
same thing as XML-RPC but with the JSON format.

We use the `version 2 of the
protocol <http://www.jsonrpc.org/specification>`__. You must call the
API with a ``POST`` HTTP request.

Kanboard support batch requests, so you can make multiple API calls in a
single HTTP request. It's particularly useful for mobile clients with
higher network latency.

Usage
-----

-  `Authentication <api-authentication.markdown>`__
-  `Examples <api-examples.markdown>`__
-  `Application <api-application-procedures.markdown>`__
-  `Projects <api-project-procedures.markdown>`__
-  `Project Permissions <api-project-permission-procedures.markdown>`__
-  `Boards <api-board-procedures.markdown>`__
-  `Columns <api-column-procedures.markdown>`__
-  `Swimlanes <api-swimlane-procedures.markdown>`__
-  `Categories <api-category-procedures.markdown>`__
-  `Automatic Actions <api-action-procedures.markdown>`__
-  `Tasks <api-task-procedures.markdown>`__
-  `Subtasks <api-subtask-procedures.markdown>`__
-  `Files <api-file-procedures.markdown>`__
-  `Links <api-link-procedures.markdown>`__
-  `Comments <api-comment-procedures.markdown>`__
-  `Users <api-user-procedures.markdown>`__
-  `Groups <api-group-procedures.markdown>`__
-  `Group Members <api-group-member-procedures.markdown>`__
-  `Me <api-me-procedures.markdown>`__


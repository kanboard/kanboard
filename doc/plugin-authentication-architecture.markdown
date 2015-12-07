Authentication Architecture
===========================

Kanboard provides a flexible and pluggable authentication architecture.

By default, user authentication can be done with multiple methods:

- Username and password authentication (Local database and LDAP)
- OAuth2 authentication
- Reverse-Proxy authentication
- Cookie based authentication (Remember Me)

More over, after a successful authentication, a Two-Factor post authentication can be done.
Kanboard supports natively the TOTP standard.

Authentication Interfaces
-------------------------

To have a pluggable system, authentication drivers must implement a set of interfaces:

| Interface                                | Role                                                             |
|------------------------------------------|------------------------------------------------------------------|
| AuthenticationProviderInterface          | Base interface for other authentication interfaces               |
| PreAuthenticationProviderInterface       | The user is already authenticated when reaching the application, web servers usually define some environment variables  |
| PasswordAuthenticationProviderInterface  | Authentication methods that uses the username and password provided in the login form  |
| OAuthAuthenticationProviderInterface     | OAuth2 providers   |
| PostAuthenticationProviderInterface      | Two-Factor auhentication drivers, ask for confirmation code        |
| SessionCheckProviderInterface            | Providers that are able to check if the user session is valid        |

### Examples of authentication providers:

- The default Database method implements `PasswordAuthenticationProviderInterface` and `SessionCheckProviderInterface`
- The Reverse-Proxy method implements `PreAuthenticationProviderInterface` and `SessionCheckProviderInterface`
- The Google method implements `OAuthAuthenticationProviderInterface`
- The LDAP method implements `PasswordAuthenticationProviderInterface`
- The RememberMe cookie method implements `PreAuthenticationProviderInterface`
- The Two-Factor TOTP method implements `PostAuthenticationProviderInterface`

Authentication Workflow
-----------------------

For each HTTP request:

1. If the user session is already open, execute registered providers that implements `SessionCheckProviderInterface`
2. Execute all providers that implements `PreAuthenticationProviderInterface`
3. If the end-user submit the login form, providers that implements `PasswordAuthenticationProviderInterface` are executed
4. If the end-user wants to use OAuth2, the selected provider will be executed
5. After a successful authentication, the last registered `PostAuthenticationProviderInterface` will be used
6. Synchronize user information if necessary

This workflow is managed by the class `Kanboard\Core\Security\AuthenticationManager`.

Events triggered:

- `AuthenticationManager::EVENT_SUCCESS`: Successful authentication
- `AuthenticationManager::EVENT_FAILURE`: Failed authentication

Each time a failure event occurs, the counter of failed logins is incremented.

The user account can be locked down for the configured period of time and a captcha can be shown to avoid brute force attacks.

User Provider Interface
-----------------------

When the authentication is successful, the `AuthenticationManager` will ask the user information to your driver by calling the method `getUser()`.
This method must return an object that implements the interface `Kanboard\Core\User\UserProviderInterface`.

This class abstract the information gathered from another system.

Examples:

- `DatabaseUserProvider` provides information for an internal user
- `LdapUserProvider` for a LDAP user
- `ReverseProxyUserProvider` for a Reverse-Proxy user
- `GoogleUserProvider` represents a Google user

Methods for User Provider Interface:

- `isUserCreationAllowed()`: Return true to allow automatic user creation
- `getExternalIdColumn()`: Get external id column name (google_id, github_id, gitlab_id...)
- `getInternalId()`: Get internal database id
- `getExternalId()`: Get external id (Unique id)
- `getRole()`: Get user role
- `getUsername()`: Get username
- `getName()`: Get user full name
- `getEmail()`: Get user email address
- `getExternalGroupIds()`: Get external group ids, automatically sync group membership if present
- `getExtraAttributes()`: Get extra attributes to set for the user during the local sync

It's not mandatory to return a value for each method.

User Local Synchronization
--------------------------

User information can be automatically synced with the local database.

- If the method `getInternalId()` return a value no synchronization is performed
- The methods `getExternalIdColumn()` and `getExternalId()` must return a value to sync the user
- Properties that returns an empty string won't be synced

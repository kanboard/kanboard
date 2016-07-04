Authentication Architecture[¶](#authentication-architecture "Ссылка на этот заголовок")

=======================================================================================



Kanboard provides a flexible and pluggable authentication architecture.



By default, user authentication can be done with multiple methods:



-   Username and password authentication (Local database and LDAP)

-   OAuth2 authentication

-   Reverse-Proxy authentication

-   Cookie based authentication (Remember Me)



More over, after a successful authentication, a Two-Factor post authentication can be done. Kanboard supports natively the TOTP standard.



Authentication Interfaces[¶](#authentication-interfaces "Ссылка на этот заголовок")

-----------------------------------------------------------------------------------



To have a pluggable system, authentication drivers must implement a set of interfaces:



  ------------------------------------------------------------------------

  Interface          Role

  ------------------ -----------------------------------------------------

  AuthenticationProv Base interface for other authentication interfaces

  iderInterface      



  PreAuthenticationP The user is already authenticated when reaching the

  roviderInterface   application, web servers usually define some

                     environment variables



  PasswordAuthentica Authentication methods that uses the username and

  tionProviderInterf password provided in the login form

  ace                



  OAuthAuthenticatio OAuth2 providers

  nProviderInterface 



  PostAuthentication Two-Factor auhentication drivers, ask for

  ProviderInterface  confirmation code



  SessionCheckProvid Providers that are able to check if the user session

  erInterface        is valid

  ------------------------------------------------------------------------



### Examples of authentication providers:[¶](#examples-of-authentication-providers "Ссылка на этот заголовок")



-   The default Database method implements `PasswordAuthenticationProviderInterface`{.docutils .literal} and `SessionCheckProviderInterface`{.docutils .literal}

-   The Reverse-Proxy method implements `PreAuthenticationProviderInterface`{.docutils .literal} and `SessionCheckProviderInterface`{.docutils .literal}

-   The Google method implements `OAuthAuthenticationProviderInterface`{.docutils .literal}

-   The LDAP method implements `PasswordAuthenticationProviderInterface`{.docutils .literal}

-   The RememberMe cookie method implements `PreAuthenticationProviderInterface`{.docutils .literal}

-   The Two-Factor TOTP method implements `PostAuthenticationProviderInterface`{.docutils .literal}



Authentication Workflow[¶](#authentication-workflow "Ссылка на этот заголовок")

-------------------------------------------------------------------------------



For each HTTP request:



1.  If the user session is already open, execute registered providers that implements `SessionCheckProviderInterface`{.docutils .literal}

2.  Execute all providers that implements `PreAuthenticationProviderInterface`{.docutils .literal}

3.  If the end-user submit the login form, providers that implements `PasswordAuthenticationProviderInterface`{.docutils .literal} are executed

4.  If the end-user wants to use OAuth2, the selected provider will be executed

5.  After a successful authentication, the last registered `PostAuthenticationProviderInterface`{.docutils .literal} will be used

6.  Synchronize user information if necessary



This workflow is managed by the class `Kanboard\Core\Security\AuthenticationManager`{.docutils .literal}.



Events triggered:



-   `AuthenticationManager::EVENT_SUCCESS`{.docutils .literal}: Successful authentication

-   `AuthenticationManager::EVENT_FAILURE`{.docutils .literal}: Failed authentication



Each time a failure event occurs, the counter of failed logins is incremented.



The user account can be locked down for the configured period of time and a captcha can be shown to avoid brute force attacks.



User Provider Interface[¶](#user-provider-interface "Ссылка на этот заголовок")

-------------------------------------------------------------------------------



When the authentication is successful, the `AuthenticationManager`{.docutils .literal} will ask the user information to your driver by calling the method `getUser()`{.docutils .literal}. This method must return an object that implements the interface `Kanboard\Core\User\UserProviderInterface`{.docutils .literal}.



This class abstract the information gathered from another system.



Examples:



-   `DatabaseUserProvider`{.docutils .literal} provides information for an internal user

-   `LdapUserProvider`{.docutils .literal} for a LDAP user

-   `ReverseProxyUserProvider`{.docutils .literal} for a Reverse-Proxy user

-   `GoogleUserProvider`{.docutils .literal} represents a Google user



Methods for User Provider Interface:



-   `isUserCreationAllowed()`{.docutils .literal}: Return true to allow automatic user creation

-   `getExternalIdColumn()`{.docutils .literal}: Get external id column name (google\_id, github\_id, gitlab\_id...)

-   `getInternalId()`{.docutils .literal}: Get internal database id

-   `getExternalId()`{.docutils .literal}: Get external id (Unique id)

-   `getRole()`{.docutils .literal}: Get user role

-   `getUsername()`{.docutils .literal}: Get username

-   `getName()`{.docutils .literal}: Get user full name

-   `getEmail()`{.docutils .literal}: Get user email address

-   `getExternalGroupIds()`{.docutils .literal}: Get external group ids, automatically sync group membership if present

-   `getExtraAttributes()`{.docutils .literal}: Get extra attributes to set for the user during the local sync



It’s not mandatory to return a value for each method.



User Local Synchronization[¶](#user-local-synchronization "Ссылка на этот заголовок")

-------------------------------------------------------------------------------------



User information can be automatically synced with the local database.



-   If the method `getInternalId()`{.docutils .literal} return a value no synchronization is performed

-   The methods `getExternalIdColumn()`{.docutils .literal} and `getExternalId()`{.docutils .literal} must return a value to sync the user

-   Properties that returns an empty string won’t be synced



### [Оглавление](index.markdown)



-   [Authentication Architecture](#)

    -   [Authentication Interfaces](#authentication-interfaces)

        -   [Examples of authentication providers:](#examples-of-authentication-providers)

    -   [Authentication Workflow](#authentication-workflow)

    -   [User Provider Interface](#user-provider-interface)

    -   [User Local Synchronization](#user-local-synchronization)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)


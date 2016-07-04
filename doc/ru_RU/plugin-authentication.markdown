Authentication Plugin[¶](#authentication-plugin "Ссылка на этот заголовок")

===========================================================================



New authentication backends can be written with very few lines of code.



Provider Registration[¶](#provider-registration "Ссылка на этот заголовок")

---------------------------------------------------------------------------



In the method `initialize()`{.docutils .literal} of your plugin, call the method `register()`{.docutils .literal} of the class `AuthenticationManager`{.docutils .literal}:



    public function initialize()

    {

        $this->authenticationManager->register(new ReverseProxyLdapAuth($this->container));

    }



The object provided to the method `register()`{.docutils .literal} must implement one of the pre-defined authentication interfaces.



Those interfaces are defined in the namepsace `Kanboard\Core\Security`{.docutils .literal}:



-   `Kanboard\Core\Security\PreAuthenticationProviderInterface`{.docutils .literal}

-   `Kanboard\Core\Security\PostAuthenticationProviderInterface`{.docutils .literal}

-   `Kanboard\Core\Security\PasswordAuthenticationProviderInterface`{.docutils .literal}

-   `Kanboard\Core\Security\OAuthAuthenticationProviderInterface`{.docutils .literal}



The only requirement is to implement the interfaces, you class can be written the way you want and located anywhere on the disk.



User Provider[¶](#user-provider "Ссылка на этот заголовок")

-----------------------------------------------------------



When the authentication is successful, your driver must return an object that represents the user. This object must implement the interface `Kanboard\Core\User\UserProviderInterface`{.docutils .literal}.



Example of authentication plugins[¶](#example-of-authentication-plugins "Ссылка на этот заголовок")

---------------------------------------------------------------------------------------------------



-   [Authentication providers included in Kanboard](https://github.com/fguillot/kanboard/tree/master/app/Auth)

-   [Reverse-Proxy Authentication with LDAP support](https://github.com/kanboard/plugin-reverse-proxy-ldap)

-   [SMS Two-Factor Authentication](https://github.com/kanboard/plugin-sms-2fa)



### [Оглавление](index.markdown)



-   [Authentication Plugin](#)

    -   [Provider Registration](#provider-registration)

    -   [User Provider](#user-provider)

    -   [Example of authentication plugins](#example-of-authentication-plugins)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)


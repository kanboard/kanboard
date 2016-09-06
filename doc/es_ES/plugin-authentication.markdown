Plugin de autenticación
=====================

EL nuevo backend de autenticación se puede escribir con muy pocas lineas de codigo.

Registro de proveedores
-----------------------

En el metodo `initialize()` nuestro plugin, llama al metodo `register()` de la clase `AuthenticationManager`:

```php
public function initialize()
{
    $this->authenticationManager->register(new ReverseProxyLdapAuth($this->container));
}
```

El objeto proveniente de el metodo  `register()` debe implementar one de las interfaces de autenticación predefidas.

Esas interfaces estan definidas en los namespace `Kanboard\Core\Security`:

- `Kanboard\Core\Security\PreAuthenticationProviderInterface`
- `Kanboard\Core\Security\PostAuthenticationProviderInterface`
- `Kanboard\Core\Security\PasswordAuthenticationProviderInterface`
- `Kanboard\Core\Security\OAuthAuthenticationProviderInterface`



El único requisito es implementar las interfaces, la clase se puede escribir de la manera deseada y situado en cualquier lugar en el disco

Usuario del proveedor
----------------------

Cuando la autenticación es satisfactoria, tu driver debe regresar un objato que representa al usuario.
Este objeto debe implementasr la interface `Kanboard\Core\User\UserProviderInterface`.

Ejemplo de plugin de autenticación
----------------------------------

- [Autenticación de proveedores incluido en kanboard](https://github.com/kanboard/kanboard/tree/master/app/Auth)
- [Reverse-Proxy Autenticación con soporte LDAP](https://github.com/kanboard/plugin-reverse-proxy-ldap)
- [Two-Factor Autenticación SMS](https://github.com/kanboard/plugin-sms-2fa)

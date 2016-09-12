Agregando un nuevo avatar del proveedor
=======================================

Registrar
------------

```php
$this->avatarManager->register(new CustomAvatarProvider());
```

Interface
---------

El proveedor debe implementar la interface `Kanboard\Core\User\Avatar\AvatarProviderInterface`:


| Metodo                     | Descripcion                                                      |
|-------------------------------|---------------------------------------------------------------|
| `render(array $user, $size)`  | Renderizar HTML                                               |
| `isActive(array $user)`       | Regresa un boolean si el proveedor esta activo                |


El argumento `$user` es un directorio que contiene estas llaves :

```php
[
    'id' => 123,
    'username' => 'admin',
    'name' => 'Administrator',
    'email' => 'me@localhost',
]
```

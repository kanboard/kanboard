Adding a new Avatar Provider
=============================

Registration
------------

```php
$this->avatarManager->register(new CustomAvatarProvider());
```

Interface
---------

The provider must implements the interface `Kanboard\Core\User\Avatar\AvatarProviderInterface`:


| Method                        | Description                                                   |
|-------------------------------|---------------------------------------------------------------|
| `render(array $user, $size)`  | Render HTML                                                   |
| `isActive(array $user)`       | Returns a boolean if the provider is able to render something |


The `$user` argument is a dictionary that contains these keys:

```php
[
    'id' => 123,
    'username' => 'admin',
    'name' => 'Administrator',
    'email' => 'me@localhost',
]
```

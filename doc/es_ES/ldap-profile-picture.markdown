LDAP Foto de perfil de usuario
==============================

Kanboard puede descargar automaticamente las fotografias del usuario desde el servidor LDAP.

Esta caracteristica esta habilitada solo si la autenticación LDAP esta actividada y el parametro `LDAP_USER_ATTRIBUTE_PHOTO` es definido.

Configuración
-------------

En tu  `config.php`, debes de agregar el atributo LDAP para almacenar la imagen.

```php
define('LDAP_USER_ATTRIBUTE_PHOTO', 'jpegPhoto');
```

Usualmente, los atributos usados son `jpegPhoto` o `thumbnailPhoto`.
La imagen puede ser almacenada en el formato JPEG o PNG.

Para subir la imagen en el perfil de usuario, los  los administradores
To upload the image in the user profile, los administradores de Active Directory pueden utilizar el software como [AD Photo Edit](http://www.cjwdev.co.uk/Software/ADPhotoEdit/Info.html).

Notas
-----

La imagen del perfil se ** descarga en el momento de inicio de sesión sólo si el usuario no tiene ya subida una imagen previamente **.

Para cambiar la foto del usuario, el anterior tiene que ser eliminado de forma manual en el perfil de usuario.

LDAP User Profile Photo
=======================

Kanboard can download automatically user pictures from the LDAP server.

This feature is enabled only if LDAP authentication is activated and the parameter `LDAP_USER_ATTRIBUTE_PHOTO` is defined.

Configuration
-------------

In your `config.php`, you have to set the LDAP attribute used to store the image.

```php
define('LDAP_USER_ATTRIBUTE_PHOTO', 'jpegPhoto');
```

Usually, the attributes `jpegPhoto` or `thumbnailPhoto` are used.
The image can be stored in JPEG or PNG format.

To upload the image in the user profile, Active Directory administrators may use software like [AD Photo Edit](http://www.cjwdev.co.uk/Software/ADPhotoEdit/Info.html).

Notes
-----

The profile image is **downloaded at login time only if the user do not already have uploaded an image previously**.

To change the user photo, the previous one have to be removed manually in the user profile.

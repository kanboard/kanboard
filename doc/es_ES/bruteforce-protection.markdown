Protección por fuerza bruta
===========================

La protección por fuerza bruta de kanboard funciona en nivel a la cuenta de usuario.

- Después de 3 fallas de autentificación para el formulario de login muestra una imagen de captcha para evitar bots automatizado orientativos.
- Después de 6 fallas de autentificación la cuenta de usuario esta bloqueada por un periodo de 15 minutos.

Esta característica funciona para el método de autentificación del usuario API, la cuenta tiene que ser desbloqueado mediante el formulario de inicio de sesión.

Sin embargo, después de la tercera falla de autenticidad a través de la API de usuario, la cuenta tiene que ser desbloqueado mediante el formulario de inicio de sesión.

Kanboard no bloquea cualquier dirección de la IP ya que los bots puede utilizar a varios servidores proxy anónimo sin embargo puede utilizar herramientas externas como f[fail2ban](http://www.fail2ban.org) para evitar la exploración masiva.

Los ajustes predeterminados se pueden cambiar con estas variables de configuración:

```php
// Habilitar captcha después del fallo 3 de autentificación 
define('BRUTEFORCE_CAPTCHA', 3);

// Bloquear la cuenta después de 6 fallo de autentificación 
define('BRUTEFORCE_LOCKDOWN', 6);

//Bloqueo de la duración de la cuenta en minutos.
define('BRUTEFORCE_LOCKDOWN_DURATION', 15)
```

Autentificación Two-Factor 
==========================

Cada usuario puede habilitar el [two-factor authentication](http://en.wikipedia.org/wiki/Two_factor_authentication).
Antes de un logeo exitoso, un codigo one-time de (6 caracteres) se le pide a el usuaio para permitir el acceso a kanboard.

Este codigo tiene que ser proporcionado por un software compatible generalmente instalado en tu smartphone

Kanboard usa el [Time-based One-time Password Algorithm](http://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) definido en el  [RFC 6238](http://tools.ietf.org/html/rfc6238).

Existen muchos softwares compatibles con el standard del sistema TOTP.
Por ejemplo, tu puedes usar estas aplicaciones libres y de open source:

- [Google Authenticator](https://github.com/google/google-authenticator/) (Android, iOS, Blackberry)
- [FreeOTP](https://freeotp.github.io/) (Android, iOS)
- [OATH Toolkit](http://www.nongnu.org/oath-toolkit/) (Utilidad en linea de comandos Unix/Linux

Este sistema puede trabajar offline y no es necesario tener un  telefono movil

Instalacion
-----------

1. Ir a tu perfil de usuario
2. Click a la izquierda en **Two factor authentication** y selecionar la caja
3. Una key secret es geradada para ti

![2FA](../screenshots/2fa.png)

- Tienes que salvar la key secret en tu software TOTP. Si usas un smartphone, la solución sera mas fácil ya que puedes escanear el Qr code con FreeOTP o Google Authenticator.
- Cada vez que abras una nueva session , un nuevo código se pedirá.
- No se olvide de verficar el dispositivo antes de cerrar la sesión

Una nueva key secret es generada cada vez que actives o desactives esta función
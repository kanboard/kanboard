API de autentificación
==================

API endpoint
------------

URL: `https://YOUR_SERVER/jsonrpc.php`


Metedo por default (HTTP Basico)
---------------------------

### Aplicación de credenciales

- Username: `jsonrpc`
- Password: API token para la configuración de pagina

### Credencial de usuario

- Usar el usuario real y su password

La API usa la [Autentificación Basica del esquema HTTP descrita en el RFC2617](http://www.ietf.org/rfc/rfc2617.txt).


Modificar el header HTTP
------------------------

Se puede usar un header HTTP alternativo para la autentificación si tu servidor es muy especifico

configuration.

- El nombre del header puede ser cualquier cosa que desee, por ejemplo `X-API-Auth`.
- El valor del header `username:password` esta codificado en Base64.

Configuración:

1. Definir tu header personalizado en tu `config.php`: `define('API_AUTHENTICATION_HEADER', 'X-API-Auth');`
2. Codificar las credenciales en Base64, ejemplo con PHP  `base64_encode('jsonrpc:19ffd9709d03ce50675c3a43d1c49c1ac207f4bc45f06c5b2701fbdf8929');`
3. Verificar con curl

```bash
curl \
-H 'X-API-Auth: anNvbnJwYzoxOWZmZDk3MDlkMDNjZTUwNjc1YzNhNDNkMWM0OWMxYWMyMDdmNGJjNDVmMDZjNWIyNzAxZmJkZjg5Mjk=' \
-d '{"jsonrpc": "2.0", "method": "getAllProjects", "id": 1}' \
http://localhost/kanboard/jsonrpc.php
```

Error de autentificación
-------------------------
Authentication error
--------------------

Si las credenciales son , recibiras  un `401 Not Authorized` y el correspondiente respuesta del JSON.
If the credentials are wrong, you will receive a `401 Not Authorized` and the corresponding JSON response.
 

Error de Autorización
----------------------

Si el usuario conectado no tiene permiso para acceder al recurso , recibirá un `403 Forbidden`.

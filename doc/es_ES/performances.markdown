Kanboard Performances
=====================

De acuerdo con su configuración, algunas características pueden alentar el uso de Kanboard.
Por defecto, todas las operaciones son síncronas y en algunos thread realizan peticiones HTTP
Esta es una limitación PHP.
Sin embargo, es posible mejorar eso.

Dependiendo de los plugins instalados, la comunicación a los servicios externos puede llevar cientos de milisegundos o incluso segundos.
Para evitar el bloqueo del thread principal, es posible delegar estas operaciones para un grupo de trabajadores [fondo] (worker.markdown).
Esta configuración requiere una instalación de software adicional en su infraestructura.


Cómo detectar un cuello de botella?
-----------------------------

- Activar el modo de depuración
- Supervisar el archivo de registro
- Hacer algo en Kanboard (arrastrar y soltar una tarea por ejemplo)
- Todas las operaciones se registran con el tiempo de ejecución (solicitudes HTTP, las notificaciones de correo electrónico, solicitudes SQL)



Mejorar la velocidad de notificaciones por correo electrónico
---------------------------------

Utilizando el método de SMTP con un servidor externo puede ser muy lento.

Soluciones posibles:

- Utilizar los trabajadores de fondo si usted aún desea utilizar SMTP
- Utilice un relé de correo electrónico local con Postfix y utilizar el transporte "correo"
- Utiliza un proveedor de correo electrónico que utilizan una API HTTP para enviar mensajes de correo electrónico (SendGrid, Mailgun o Postmark)


Mejorar el rendimiento Sqlite
---------------------------

Soluciones posibles:

- No utilizar SQLite cuando se tiene una gran cantidad de concurrencia (varios usuarios), seleccione Postgres o MySQL en vez
- No utilizar SQLite en un montaje NFS compartido
- No utilizar SQLite en un disco con IOPS pobres, siempre es preferible utilizar unidades SSD locales

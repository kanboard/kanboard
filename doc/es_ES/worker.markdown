Background Workers
==================

**Esta caracteristica es experimental**.

Dependiendo de tu configuración, algunas caracteristicas pueden hacer lenta la aplicacion si se esta ejecutando algunos procesos como peticiones HTTP.
Kanboard puede delegar aquella tarea a un background worker que escucha los eventos entrantes.

Ejemplo de caracteristicas que hacen lento a kanboard:

- El envío de correos electrónicos a través de un servidor SMTP externo puede tardar varios segundos
- Envío de notificaciones a servicios externos

Esta característica es opcional y requiere la instalación de un demonio de cola en el servidor.

### Beanstalk

[Beanstalk](http://kr.github.io/beanstalkd/) es una sencilla cola de trabajo, rápido.

- Para instalar Beanstalk, puede simplemente usar el gestor de paquetes de su distribución de Linux
- Install el [Kanboard plugin para Beanstalk](https://kanboard.net/plugin/beanstalk)
- Iniciar el trabajador con la herramienta de línea de comandos Kanboard: `./kanboard worker`

### RabbitMQ

[RabbitMQ](https://www.rabbitmq.com/) es un sistema de mensajería robusta que es más adecuado para la infraestructura de alta disponibilidad..

- Sigue la documentación oficial de RabbitMQ para la instalación y la configuración
- Instalar el [Kanboard plugin para RabbitMQ](https://kanboard.net/plugin/rabbitmq)
- Iniciar el trabajador con la herramienta de línea de comandos Kanboard: `./kanboard worker`

### Notes

- Debe comenzar el trabajador Kanboard con un supervisor de procesos (systemd, upstart or supervisord)
- El proceso debe ser tiene acceso a la carpeta de datos si almacena los archivos en el sistema de archivos local y tienen SQLite

Crear tareas por email
=====================

Tu puedes crear tareas directamente enviando un email.
Esta caracteristica esta disponible usando plugins.


Por el momento, Kanboard está integrado con 3 servicios externos:

- [Mailgun](https://github.com/kanboard/plugin-mailgun)
- [Sendgrid](https://github.com/kanboard/plugin-sendgrid)
- [Postmark](https://github.com/kanboard/plugin-postmark)

Estos servicios manejan mensajes de correo electrónico entrantes sin necesidad de configurar cualquier servidor SMTP.

Cuando se recibe un correo electrónico, Kanboard recibido el mensaje a un punto final específico.
Todos los trabajos complicados ya son manejados por esos servicios.

Workflow de correos electrónicos entrantes 
------------------------------------------

1. Se envía un correo electrónico a una dirección específica, por ejemplo **something+myproject@inbound.mydomain.tld**
2. Su correo electrónico se envía a los servidores SMTP de terceros
3. Los proveedores SMTP llaman a un web hook del Kanboard con el email en JSON o en formatos multipart/form-data
4. Kanboard analiza el correo electrónico recibido y creaa la tarea para el proyecto.

Nota: Las tareas nuevas son automaticamente creadas en la primera columna.

Formato de Email
----------------

- La parte local de la dirección de correo electrónico debe utilizar el separador además, por ejemplo **kanboard+project123**
- La cadena definida después del signo más [+] debe coincidir con un identificador de proyecto, por ejemplo **project123** si el identificador del proyecto es **Project 123**
- El asunto del correo electrónico se convierte en el título de la tarea
- El cuerpo del correo electrónico se convierte en la descripción de la tarea (formato Markdown)

Los correos electrónicos entrantes se pueden escribir en formatos de texto o HTML.
**Kanboard es capaz de convertir mensajes de correo electrónico HTML en lenguaje Markdown**.

La seguridad y los requisitos
-------------------------

- El web hook de Kanboard está protegido por un token aleatorio
- La dirección de correo electrónico del remitente debe coincidir con un usuario Kanboard
- El proyecto Kanboard debe tener un identificador único, por ejemplo **MYPROJECT** 
- El usuario Kanboard debe ser un miembro del proyecto


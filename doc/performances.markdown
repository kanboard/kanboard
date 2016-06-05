Kanboard Performances
=====================

According to your configuration, some features can slow down the usage of Kanboard.
By default, all operations are synchronous and performed in the same thread as the HTTP request.
This is a PHP limitation.
However, it's possible to improve that.

Depending on the plugins you install, communicating to external services can take hundred of milliseconds or even seconds.
To avoid blocking the main thread, it's possible to delegate these operations to a pool of [background workers](worker.markdown).
This setup require that you install additional software in your infrastructure.

How to detect the bottleneck?
-----------------------------

- Enable the debug mode
- Monitor the log file
- Do something in Kanboard (drag and drop a task for example)
- All operations are logged with the execution time (HTTP requests, Email notifications, SQL requests)

Improve Email notifications speed
---------------------------------

Using the SMTP method with an external server can be very slow.

Possible solutions:

- Use the background workers if you still want to use SMTP
- Use a local email relay with Postfix and use the "mail" transport
- Use an email provider that use an HTTP API to send emails (Sendgrid, Mailgun or Postmark)

Improve Sqlite performances
---------------------------

Possible solutions:

- Do not use Sqlite when you have a lot of concurrency (several users), choose Postgres or Mysql instead
- Do not use Sqlite on a shared NFS mount
- Do not use Sqlite on a disk with poor IOPS, it's always preferable to use local SSD drives

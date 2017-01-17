Background Workers
==================

**This feature is experimental**.

Depending on your configuration, some features can slow down the application if they are executed in the same process as the HTTP request.
Kanboard can delegate these tasks to a background worker that listen for incoming events.

Example of feature that may slow down Kanboard:

- Sending emails via an external SMTP server can take several seconds
- Sending notifications to external services

This feature is optional and require the installation of a queue daemon on your server.

### Beanstalk

[Beanstalk](http://kr.github.io/beanstalkd/) is a simple, fast work queue.

- To install Beanstalk, you can simply use the package manager of your Linux distribution
- Install the [Kanboard plugin for Beanstalk](https://kanboard.net/plugin/beanstalk)
- Start the worker with the Kanboard command line tool: `./cli worker`

### RabbitMQ

[RabbitMQ](https://www.rabbitmq.com/) is a robust messaging system that is more suitable for high-availability infrastructure.

- Follow the official documentation of RabbitMQ for the installation and the configuration
- Install the [Kanboard plugin for RabbitMQ](https://kanboard.net/plugin/rabbitmq)
- Start the worker with the Kanboard command line tool: `./cli worker`

### Notes

- You should start the Kanboard worker with a process supervisor (systemd, upstart or supervisord)
- The process must have access to the data folder if you store files on the local filesystem or use Sqlite

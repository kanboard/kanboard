How to test Kanboard with Docker?
=================================

Kanboard can run with [Docker](https://www.docker.com).
There is a `Dockerfile` in the repository to build your own container.

Build your own Docker image
---------------------------

From your kanboard directory run the following command:

```bash
docker build -t youruser/kanboard:master .
```

To run your image in background on the port 80:

```bash
docker run -d --name kanboard -p 80:80 -t youruser/kanboard:master
```

Store your data on a volume
---------------------------

You can also save your data outside of the container, on the local machine:

```bash
docker run -d --name kanboard -v /your/local/data/folder:/var/www/html/data -p 80:80 -t kanboard/kanboard:master
```

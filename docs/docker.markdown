How to test Kanboard with Docker?
=================================

Kanboard can run with [Docker](https://www.docker.com).
You can use the public image or build your own image from the `Dockerfile`.

Actually, the Docker image is based on the master branch (development version).

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

Run the public Kanboard image
-----------------------------

This image is stored on the [Docker Hub](https://hub.docker.com).

Fetch the image on your machine:

```bash
docker pull kanboard/kanboard:master
```

Run the image:

```bash
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:master
```

Note: This image is not necessary up to date

Store your data on a volume
---------------------------

You can also save your data outside of the container, on the local machine:

```bash
docker run -d --name kanboard -v /your/local/data/folder:/var/www/html/data -p 80:80 -t kanboard/kanboard:master
```

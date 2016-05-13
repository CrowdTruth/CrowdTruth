# Dockerized CrowdTruth

Before starting to use the dockerized version of CrowdTruth, make sure you have Docker and are able to run docker containers. Use [the docker documentation](https://docs.docker.com/linux/) to get familiar with it first.

A dockerized version of CrowdTruth is available via [Dockerhub](https://hub.docker.com/r/crowdtruth/crowdtruth/). You can pull the docker image directly from DockerHub:
```
docker pull crowdtruth/crowdtruth
```

CrowdTruth needs to connect to a MongoDB database. The container will attempt to connect to MongoDB on host `crowdtruth-mongo` (although you can override this by supplying the container with a configuration file). You can start a container MongoDB with a blank database like this:
```
docker run  -d --name crowdtruth-mongo mongo
```

Now you can start the CrowdTruth docker container and connect it to the MongoDB container:
```
docker run -d --name crowdtruth-local -p 8080:80 --link crowdtruth-mongo crowdtruth/crowdtruth
```

If everything went as expected, your CrowdTruth instance should be visible at `http://localhost:8080/`. If you want to run on a different port, modify docker's port publishing (`-p 8080:80`). If you cannot connect to localhost, try connecting to the IP address of your machine. You can find the IP of your machine using the command `docker-machine ip default`

## Persistent storage

If you want to load your own data follow these steps (see [where to store data](https://hub.docker.com/_/mongo/) for more info):

Start mongo container, telling the container where to find your data:

```
docker run  -d --name crowdtruth-mongo -v /path/to/your/data/dir:/data/db mongo
```

Then start your crowdtruth container as usual:
```
docker run -d --name crowdtruth-local -p 8080:80 --link crowdtruth-mongo crowdtruth/crowdtruth
```

Notice that your data will be saved in `/path/to/your/data/dir` -- if you have an existing database (exported as json) you may want to import it (using the mongoimport command).

## TODO's
 - How to supply the CT container with your own database configuration file.
 - How to put code from your local host on the docker container (development mode).

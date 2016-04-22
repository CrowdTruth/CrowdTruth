# Dockerized CrowdTruth

Before starting to use the dockerized version of CrowdTruth, make sure you have Docker and are able to run docker containers. Use [the docker documentation](https://docs.docker.com/linux/) to get familiar with it first.

A dockerized version of CrowdTruth is available via [Dockerhub](https://hub.docker.com/r/crowdtruth/crowdtruth/). You can pull the docker image directly from DockerHub:
```
docker pull crowdtruth/crowdtruth
```

CrowdTruth needs to connect to a MongoDB database. The container will attempt to connect to MongoDB on host `crowdtruth-mongo` (although you can override this by supplying the container with a configuration file). You can start a container MongoDB with a blank database like this:
```
docker run --name crowdtruth-mongo -d mongo
```

Now you can start the CrowdTruth docker container and connect it to the MongoDB container:
```
docker run -d --name=crowdtruth -p 8080:80 --link crowdtruth-mongo crowdtruth/crowdtruth
```

If everything went as expected, your CrowdTruth instance should be visible at `http://localhost:8080/`. If you want to run on a different port, modify docker's port publishing (`-p 8080:80`).

## TODO's
 - How to supply the CT container with your own database configuration file.
 - How do you add persistent storage to a MongoDB container
 - How to put code from your local host on the docker container (development mode).

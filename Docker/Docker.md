You can build your own copy of Crowdtruth:

  docker build -t crowdtruth/crowdtruth -f Dockerfile .

Alternatively you can pull it from Docker Hub (not really necessary, I think...):

  docker pull crowdtruth/crowdtruth

Start a MongoDB docker container

  docker run --name crowdtruth-mongo -d mongo

Start the CrowdTruth docker container (connected to mongo)

  docker run -d --name=crowdtruth -p 8080:80 --link crowdtruth-mongo crowdtruth/crowdtruth

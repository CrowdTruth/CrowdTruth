docker build -t crowdtruth/crowdtruth_run -f Docker/Dockerfile .

docker run --name crowdtruth-mongo -d mongo

docker run -d --name=crowdtruth -p 8080:80 --link crowdtruth-mongo crowdtruth/crowdtruth_run

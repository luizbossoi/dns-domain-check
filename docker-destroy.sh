./docker-stop.sh
docker rmi $(docker images -a -q)

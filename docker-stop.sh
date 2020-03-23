docker rm php_web -f
docker ps -a | grep Exit | cut -d ' ' -f 1 | xargs docker rm
docker images | cut -d ' ' -f 1 | xargs docker rmi -f 

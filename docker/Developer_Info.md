# Set up developer server with docker #

## What is it about? ##
To develop or test in this project, you can use a docker setup.

## pre-condition ##

1. Make sure you have docker and docker-compose installed on you maschine
2. cd on system console folder `docker/docker-compose` and type in `docker-compose up -d`
3. Install composer dependencies with `docker exec nginx_caching_proxy composer install`
4. Run the unittest with `docker exec nginx_caching_proxy composer test`
5. see demopage on localhost:8090/demo in you local browser
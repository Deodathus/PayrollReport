DOCKER_BASH=docker exec -it payroll_report-php
BIN_CONSOLE=php bin/console

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

install:
	${DOCKER_BASH} composer install
	${DOCKER_BASH} ${BIN_CONSOLE} d:m:m

bash:
	${DOCKER_BASH} bash

pu:
	${DOCKER_BASH} composer phpunit
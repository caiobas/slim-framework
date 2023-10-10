up:
	docker-compose up -d --build

stop:
	docker-compose stop

down:
	docker-compose down

init:
	docker-compose exec php composer install
	docker-compose exec php php cli.php migrations:migrate

migrate:
	docker-compose exec php php cli.php migrations:migrate

console:
	docker compose exec php /bin/bash

update:
	docker-compose exec php composer install

test:
	docker-compose exec php composer test

queue-email:
	docker-compose exec php php config/RabbitMQ/email_receiver.php

DOCKER_COMPOSE_PATH=.local/docker-compose.yml
DOCKER_COMPOSE_CMD=docker compose -f $(DOCKER_COMPOSE_PATH)
DOCKER_COMPOSE_RUN_CMD=$(DOCKER_COMPOSE_CMD) run --rm 

IMAGE_NAME=ctpnhook:local

.PHONY: test
test: 
	$(DOCKER_COMPOSE_RUN_CMD) php-cli php ./vendor/bin/phpunit

.PHONY: test-laravel10
test-laravel10: 
	$(DOCKER_COMPOSE_RUN_CMD) laravel10 php ./vendor/bin/phpunit

.PHONY: composer-install
composer-install: 
	$(DOCKER_COMPOSE_RUN_CMD) php-cli composer install

.PHONY: composer-install-laravel10
composer-install-laravel10: 
	$(DOCKER_COMPOSE_RUN_CMD) laravel10 composer install

.PHONY: shell
shell:
	$(DOCKER_COMPOSE_RUN_CMD) php-cli bash

.PHONY: cs-fixer
cs-fixer:
	$(DOCKER_COMPOSE_RUN_CMD) php-cli php ./vendor/bin/php-cs-fixer fix

# Docker build
.PHONY: db
db:
	docker build -t $(IMAGE_NAME) .

.PHONY: up
up:
	$(DOCKER_COMPOSE_CMD) up -d

.PHONY: down
down:
	$(DOCKER_COMPOSE_CMD) up -d
# Detect system
UNAME_S := $(shell uname -s)
UNAME_M := $(shell uname -m)

ifeq ($(UNAME_S),Darwin)
    # Apple Silicon check
    ifeq ($(UNAME_M),arm64)
        DOCKER_COMPOSE_FILE := docker-compose-apple.yaml
    else
        DOCKER_COMPOSE_FILE := docker-compose.yaml
    endif
else
    DOCKER_COMPOSE_FILE := docker-compose.yaml
endif

ROOT_PATH := $(shell pwd)
COMPOSE := docker compose -f $(DOCKER_COMPOSE_FILE)

.PHONY: help optimize rebuild-restart composer-install composer-dump-autoload env-setup

help:
	@echo "Available commands:"
	@echo "  optimize               - Run php artisan optimize inside the container"
	@echo "  rebuild-restart        - Rebuild and restart the docker containers"
	@echo "  composer-install       - Run composer install inside the container"
	@echo "  composer-dump-autoload - Run composer dump-autoload inside the container"
	@echo "  env-setup              - Create .env and generate app key"

optimize:
	$(COMPOSE) exec app php artisan optimize

rebuild-restart:
	$(COMPOSE) down --remove-orphans
	$(COMPOSE) up -d --build --force-recreate

composer-install:
	$(COMPOSE) exec app composer install

composer-dump-autoload:
	$(COMPOSE) exec app composer dump-autoload

env-setup:
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
		echo ".env created from .env.example"; \
	fi
	$(COMPOSE) exec app php artisan key:generate

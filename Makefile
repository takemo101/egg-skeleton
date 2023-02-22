default: help

## MySQLのデータを格納するディレクトリパス
MYSQL_DATA_PATH = mysql

#### other ####
.PHONY: setup
setup: ## コマンド実行に必要なセットアップをする
	cp .env.example .env
	mkdir ${MYSQL_DATA_PATH}

.PHONY: help
help: ## display help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'


#### for app ####
.PHONY: start
start: ## docker compose up -d
	docker compose up -d egg-app

.PHONY: stop
stop: ## docker compose down
	docker compose down

.PHONY: shell
shell: ## docker compose exec
	docker compose exec egg-app bash


#### for php ####
.PHONY: install
install: ## composer install
	docker compose run --rm composer install --ignore-platform-reqs

.PHONY: update
update: ## composer update
	docker compose run --rm composer update --ignore-platform-reqs

.PHONY: autoload
autoload: ## composer dump-autoload
	docker compose run --rm composer dump-autoload

.PHONY: require
require:
ifdef package ## composer require [option=composer-option] [package=composer-package]
	docker-compose run --rm composer require ${option} ${package}
else
	@echo "error: Please set the [package] variable!"
endif

.PHONY: remove
remove: ## composer remove [option=composer-option] [package=composer-package]
ifdef package
	docker-compose run --rm composer remove ${option} ${package}
else
	@echo "error: Please set the [package] variable!"
endif

.PHONY: test
test:  ## ./vendor/bin/phpunit [filter=class-name]
ifdef filter
	docker-compose run --rm php ./vendor/bin/phpunit --filter ${filter}
else
	docker-compose run --rm php ./vendor/bin/phpunit
endif

.PHONY: phpstan
phpstan: ## phpstan analyse
	docker compose run --rm phpstan analyse

.PHONY: analyze
analyze: ## ./vendor/bin/phpstan analyse
	docker compose run --rm php ./vendor/bin/phpstan analyse --memory-limit=1G

.PHONY: fix
fix: ## ./vendor/bin/php-cs-fixer fix
	docker compose run --rm php ./vendor/bin/php-cs-fixer fix -v --diff

#### for gcr ####
.PHONY: gcr-build
gcr-build: ## gcr docker build
	docker image build -t egg-skeleton .

.PHONY: gcr-start
gcr-start: ## gcr docker run
	docker run --name egg -d -p 8080:8080 egg-skeleton

.PHONY: gcr-stop
gcr-stop: ## gcr docker run
	docker stop egg
	docker rm egg

.PHONY: create-deploy-shell
create-deploy-shell: ## create gcr deploy shell
	cp deploy.example.sh deploy.sh
	chmod +x deploy.sh

default: help

.PHONY: setup
setup: ## setup
	cp .example.env .env

.PHONY: help
help: ## display help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#### for php ####
.PHONY: install
install: ## composer install
	docker-compose run --rm composer install --ignore-platform-reqs

.PHONY: update
update: ## composer update
	docker-compose run --rm composer update --ignore-platform-reqs

.PHONY: autoload
autoload: ## composer dump-autoload
	docker-compose run --rm composer dump-autoload

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
test: ## phpunit test
	docker-compose run --rm php ./vendor/bin/phpunit

.PHONY: phpstan
phpstan: ## phpstan analyze by docker
	docker-compose run --rm phpstan analyse

.PHONY: analyze
analyze: ## phpstan analyze by vendor
	docker-compose run --rm php ./vendor/bin/phpstan analyse

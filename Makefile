.PHONY: install test server help phpcbf php-cs-fixer git-commit git-push

.DEFAULT_GOAL= help

CURRENT_DIR=$(shell pwd)
PORT?=8000
HOST=127.0.0.1

COM_COLOR   = \033[0;34m
OBJ_COLOR   = \033[0;36m
OK_COLOR    = \033[0;32m
ERROR_COLOR = \033[0;31m
WARN_COLOR  = \033[0;33m
NO_COLOR    = \033[m

composer.lock: composer.json ## Composer update
	composer update

vendor: composer.lock ## Composer install
	composer install

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-10s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

install: vendor ## Application install

test: install ## Start unit tests
	php ./vendor/bin/phpunit --colors

phpcbf:
	php ./vendor/bin/phpcbf

php-cs-fixer:
	php ./vendor/bin/php-cs-fixer fix

git-commit:
	git commit -am "build by Makefile"

git-push:
	git push

build: install phpcbf php-cs-fixer test git-commit git-push ## Build for git push

server: install ## Lance le serveur interne de PHP
	echo -e "Lancement du serveur sur $(OK_COLOR)http://$(HOST):$(PORT)$(NO_COLOR)"
	ENV=dev php -S $(HOST):$(PORT) -d display_errors=1
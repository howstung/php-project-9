PORT ?= 8080

install:
		composer install

validate:
		composer validate

lint:
		composer exec --verbose phpcs -- --standard=PSR12 src public

lint-fix:
		composer exec phpcbf -- --standard=PSR12 -v src public

start:
		PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

dev:
		php -S localhost:8000 -t public
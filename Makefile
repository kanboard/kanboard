DOCKER_IMAGE := kanboard/kanboard
DOCKER_TAG := master
VERSION := $(shell git rev-parse --short HEAD)

.PHONY: all
all: static

.PHONY: clean
clean:
	@ rm -rf ./node_modules

.PHONY: static
static: clean
	@ npm install
	@ ./node_modules/.bin/gulp vendor js css
	@ ./node_modules/.bin/jshint assets/js/{core,components,polyfills}

.PHONY: jshint
jshint:
	@ ./node_modules/.bin/jshint assets/js/{core,components,polyfills}

.PHONY: archive
archive:
	@ echo "Build archive: version=$(VERSION)"
	@ git archive --format=zip --prefix=kanboard/ $(VERSION) -o kanboard-$(VERSION).zip

.PHONY: test-sqlite
test-sqlite:
	@ ./vendor/bin/phpunit -c tests/units.sqlite.xml

.PHONY: test-mysql
test-mysql:
	@ ./vendor/bin/phpunit -c tests/units.mysql.xml

.PHONY: test-postgres
test-postgres:
	@ ./vendor/bin/phpunit -c tests/units.postgres.xml

.PHONY: test-browser
test-browser:
	@ ./vendor/bin/phpunit -c tests/acceptance.xml

.PHONY: integration-test-mysql
integration-test-mysql:
	@ composer install --dev
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml build
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml up -d mysql app
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml up tests
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml down

.PHONY: integration-test-postgres
integration-test-postgres:
	@ composer install --dev
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml build
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml up -d postgres app
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml up tests
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml down

.PHONY: integration-test-sqlite
integration-test-sqlite:
	@ composer install --dev
	@ docker-compose -f tests/docker/compose.integration.sqlite.yaml build
	@ docker-compose -f tests/docker/compose.integration.sqlite.yaml up -d app
	@ docker-compose -f tests/docker/compose.integration.sqlite.yaml up tests
	@ docker-compose -f tests/docker/compose.integration.sqlite.yaml down

.PHONY: sql
sql:
	@ pg_dump -x -O --schema-only --no-owner --no-privileges --quote-all-identifiers -n public --file app/Schema/Sql/postgres.sql kanboard
	@ pg_dump -d kanboard --column-inserts --data-only --table settings >> app/Schema/Sql/postgres.sql
	@ pg_dump -d kanboard --column-inserts --data-only --table links >> app/Schema/Sql/postgres.sql

	@ mysqldump -uroot --quote-names --no-create-db --skip-comments --no-data --single-transaction kanboard | sed 's/ AUTO_INCREMENT=[0-9]*//g' > app/Schema/Sql/mysql.sql
	@ mysqldump -uroot --quote-names --no-create-info --skip-comments --no-set-names kanboard settings >> app/Schema/Sql/mysql.sql
	@ mysqldump -uroot --quote-names --no-create-info --skip-comments --no-set-names kanboard links >> app/Schema/Sql/mysql.sql

	@ php -r "echo 'INSERT INTO users (username, password, role) VALUES (\'admin\', \''.password_hash('admin', PASSWORD_DEFAULT).'\', \'app-admin\');'.PHP_EOL;" | \
	tee -a app/Schema/Sql/postgres.sql app/Schema/Sql/mysql.sql >/dev/null

	@ let mysql_version=`echo 'select version from schema_version;' | mysql -N -uroot kanboard` ;\
	echo "INSERT INTO schema_version VALUES ('$$mysql_version');" >> app/Schema/Sql/mysql.sql

	@ let pg_version=`psql -U postgres -A -c 'copy(select version from schema_version) to stdout;' kanboard` ;\
	echo "INSERT INTO schema_version VALUES ('$$pg_version');" >> app/Schema/Sql/postgres.sql

	@ grep -v "SET idle_in_transaction_session_timeout = 0;" app/Schema/Sql/postgres.sql > temp && mv temp app/Schema/Sql/postgres.sql

.PHONY: docker-image
docker-image:
	@ docker build --build-arg VERSION=$(VERSION) -t $(DOCKER_IMAGE):$(DOCKER_TAG) .

.PHONY: docker-run
docker-run:
	@ docker run --rm --name=kanboard -p 80:80 -p 443:443 $(DOCKER_IMAGE):$(DOCKER_TAG)

.PHONY: docker-sh
docker-sh:
	@ docker exec -ti kanboard bash

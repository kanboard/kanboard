all: static

clean:
	@ rm -rf ./node_modules ./bower_components

static: clean
	@ npm install
	@ ./node_modules/.bin/gulp bower
	@ ./node_modules/.bin/gulp vendor js css
	@ ./node_modules/.bin/jshint assets/js/{core,components,polyfills}

jshint:
	@ ./node_modules/.bin/jshint assets/js/{core,components,polyfills}

archive:
	@ echo "Build archive: version=${version}, destination=${dst}"
	@ git archive --format=zip --prefix=kanboard/ ${version} -o ${dst}/kanboard-${version}.zip

test-sqlite-coverage:
	@ ./vendor/bin/phpunit --coverage-html /tmp/coverage --whitelist app/ -c tests/units.sqlite.xml

test-sqlite:
	@ ./vendor/bin/phpunit -c tests/units.sqlite.xml

test-mysql:
	@ ./vendor/bin/phpunit -c tests/units.mysql.xml

test-postgres:
	@ ./vendor/bin/phpunit -c tests/units.postgres.xml

test-browser:
	@ ./vendor/bin/phpunit -c tests/acceptance.xml

integration-test-mysql:
	@ composer install --dev
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml build
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml up -d mysql app
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml up tests
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml down

integration-test-postgres:
	@ composer install --dev
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml build
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml up -d postgres app
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml up tests
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml down

integration-test-sqlite:
	@ composer install --dev
	@ docker-compose -f tests/docker/compose.integration.sqlite.yaml build
	@ docker-compose -f tests/docker/compose.integration.sqlite.yaml up -d app
	@ docker-compose -f tests/docker/compose.integration.sqlite.yaml up tests
	@ docker-compose -f tests/docker/compose.integration.sqlite.yaml down

sql:
	@ pg_dump --schema-only --no-owner --no-privileges --quote-all-identifiers -n public --file app/Schema/Sql/postgres.sql kanboard
	@ pg_dump -d kanboard --column-inserts --data-only --table settings >> app/Schema/Sql/postgres.sql
	@ pg_dump -d kanboard --column-inserts --data-only --table links >> app/Schema/Sql/postgres.sql

	@ mysqldump -uroot --quote-names --no-create-db --skip-comments --no-data --single-transaction kanboard | sed 's/ AUTO_INCREMENT=[0-9]*//g' > app/Schema/Sql/mysql.sql
	@ mysqldump -uroot --quote-names --no-create-info --skip-comments --no-set-names kanboard settings >> app/Schema/Sql/mysql.sql
	@ mysqldump -uroot --quote-names --no-create-info --skip-comments --no-set-names kanboard links >> app/Schema/Sql/mysql.sql

	@ php -r "echo 'INSERT INTO users (username, password, role) VALUES (\'admin\', \''.password_hash('admin', PASSWORD_DEFAULT).'\', \'app-admin\');';" | \
	tee -a app/Schema/Sql/postgres.sql app/Schema/Sql/mysql.sql >/dev/null

	@ let mysql_version=`echo 'select version from schema_version;' | mysql -N -uroot kanboard` ;\
	echo "INSERT INTO schema_version VALUES ('$$mysql_version');" >> app/Schema/Sql/mysql.sql

	@ let pg_version=`psql -U postgres -A -c 'copy(select version from schema_version) to stdout;' kanboard` ;\
	echo "INSERT INTO schema_version VALUES ('$$pg_version');" >> app/Schema/Sql/postgres.sql

	@ grep -v "SET idle_in_transaction_session_timeout = 0;" app/Schema/Sql/postgres.sql > temp && mv temp app/Schema/Sql/postgres.sql

docker-image:
	@ IMAGE_NAME=kanboard/kanboard:latest ./hooks/build

.PHONY: all

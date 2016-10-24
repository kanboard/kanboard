BUILD_DIR = /tmp

all: static

clean:
	@ rm -rf ./node_modules ./bower_components

static: clean
	@ npm install
	@ ./node_modules/.bin/gulp bower
	@ ./node_modules/.bin/gulp vendor js css

archive:
	@ echo "Build archive: version=${version}, destination=${dst}"
	@ rm -rf ${BUILD_DIR}/kanboard ${BUILD_DIR}/kanboard-*.zip
	@ cd ${BUILD_DIR} && git clone --depth 1 -q https://github.com/kanboard/kanboard.git
	@ cd ${BUILD_DIR}/kanboard && composer --prefer-dist --no-dev --optimize-autoloader --quiet install
	@ rm -rf ${BUILD_DIR}/kanboard/data/*.sqlite
	@ rm -rf ${BUILD_DIR}/kanboard/data/*.log
	@ rm -rf ${BUILD_DIR}/kanboard/data/files
	@ rm -rf ${BUILD_DIR}/kanboard/.git*
	@ rm -rf ${BUILD_DIR}/kanboard/tests
	@ rm -rf ${BUILD_DIR}/kanboard/Makefile
	@ rm -rf ${BUILD_DIR}/kanboard/Vagrantfile
	@ rm -rf ${BUILD_DIR}/kanboard/Dockerfile
	@ rm -rf ${BUILD_DIR}/kanboard/docker-compose.yml
	@ rm -rf ${BUILD_DIR}/kanboard/.*.yml
	@ rm -rf ${BUILD_DIR}/kanboard/*.md
	@ rm -rf ${BUILD_DIR}/kanboard/*.markdown
	@ rm -rf ${BUILD_DIR}/kanboard/*.lock
	@ rm -rf ${BUILD_DIR}/kanboard/*.json
	@ rm -rf ${BUILD_DIR}/kanboard/*.js
	@ rm -rf ${BUILD_DIR}/kanboard/.dockerignore
	@ rm -rf ${BUILD_DIR}/kanboard/docker
	@ rm -rf ${BUILD_DIR}/kanboard/nitrous*
	@ cd ${BUILD_DIR}/kanboard && find ./vendor -name doc -type d -exec rm -rf {} +;
	@ cd ${BUILD_DIR}/kanboard && find ./vendor -name notes -type d -exec rm -rf {} +;
	@ cd ${BUILD_DIR}/kanboard && find ./vendor -name test -type d -exec rm -rf {} +;
	@ cd ${BUILD_DIR}/kanboard && find ./vendor -name tests -type d -exec rm -rf {} +;
	@ find ${BUILD_DIR}/kanboard/vendor -name composer.json -delete
	@ find ${BUILD_DIR}/kanboard/vendor -name phpunit.xml -delete
	@ find ${BUILD_DIR}/kanboard/vendor -name .travis.yml -delete
	@ find ${BUILD_DIR}/kanboard/vendor -name README.* -delete
	@ find ${BUILD_DIR}/kanboard/vendor -name .gitignore -delete
	@ cd ${BUILD_DIR}/kanboard && sed -i.bak 11s/.*/"define('APP_VERSION', '${version}');"/g app/constants.php && rm -f app/*.bak
	@ cd ${BUILD_DIR} && zip -r kanboard-${version}.zip kanboard > /dev/null
	@ cd ${BUILD_DIR} && mv kanboard-${version}.zip ${dst}
	@ cd ${dst} && if [ -L kanboard-latest.zip ]; then unlink kanboard-latest.zip; ln -s kanboard-${version}.zip kanboard-latest.zip; fi
	@ rm -rf ${BUILD_DIR}/kanboard

test-sqlite-coverage:
	@ ./vendor/bin/phpunit --coverage-html /tmp/coverage --whitelist app/ -c tests/units.sqlite.xml

test-sqlite:
	@ ./vendor/bin/phpunit -c tests/units.sqlite.xml

test-mysql:
	@ ./vendor/bin/phpunit -c tests/units.mysql.xml

test-postgres:
	@ ./vendor/bin/phpunit -c tests/units.postgres.xml

unittest: test-sqlite test-mysql test-postgres

test-browser:
	@ ./vendor/bin/phpunit -c tests/acceptance.xml

integration-test-mysql:
	@ composer install
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml build
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml up -d mysql app
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml up tests
	@ docker-compose -f tests/docker/compose.integration.mysql.yaml down

integration-test-postgres:
	@ composer install
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml build
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml up -d postgres app
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml up tests
	@ docker-compose -f tests/docker/compose.integration.postgres.yaml down

integration-test-sqlite:
	@ composer install
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

docker-image:
	@ docker build -t kanboard/kanboard:latest .

docker-push:
	@ docker push kanboard/kanboard:latest

docker-run:
	@ docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:latest

.PHONY: all

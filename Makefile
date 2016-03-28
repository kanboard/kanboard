BUILD_DIR = /tmp

CSS_APP = $(addprefix assets/css/src/, $(addsuffix .css, base links title table form button alert tooltip header board task comment subtask tasklink markdown listing activity dashboard pagination popover confirm sidebar responsive dropdown upload filters gantt project files views accordion avatar))
CSS_PRINT = $(addprefix assets/css/src/, $(addsuffix .css, print links table board task comment subtask tasklink markdown))
CSS_VENDOR = $(addprefix assets/css/vendor/, $(addsuffix .css, jquery-ui.min jquery-ui-timepicker-addon.min chosen.min fullcalendar.min font-awesome.min c3.min simplemde.min))

JS_APP = $(addprefix assets/js/src/, $(addsuffix .js, Namespace App Dropdown Popover Notification Accordion Session Calendar AvgTimeColumnChart BurndownChart CompareHoursColumnChart CumulativeFlowDiagram LeadCycleTimeChart UserRepartitionChart TaskTimeColumnChart TaskRepartitionChart Gantt Column Markdown ProjectPermission ProjectCreation Screenshot FileUpload Search Task Subtask Swimlane BoardColumnView BoardColumnScrolling BoardHorizontalScrolling BoardCollapsedMode BoardDragAndDrop BoardTask BoardPolling Tooltip Bootstrap))
JS_VENDOR = $(addprefix assets/js/vendor/, $(addsuffix .js, jquery-1.11.3.min jquery-ui.min jquery-ui-timepicker-addon.min jquery.ui.touch-punch.min chosen.jquery.min moment.min fullcalendar.min mousetrap.min mousetrap-global-bind.min simplemde.min))
JS_LANG = $(addprefix assets/js/vendor/lang/, $(addsuffix .js, cs da de es el fi fr hu id it ja ko nl nb pl pt pt-br ru sv sr th tr zh-cn))

all: css js

vendor.css:
	@ cat ${CSS_VENDOR} > vendor.css

app.css:
	@ rm -f assets/css/app.css
	@ cat ${CSS_APP} > tmp.css
	@ yuicompressor --charset utf-8 --type css -o tmp.css tmp.css
	@ cat vendor.css tmp.css >> assets/css/app.css
	@ rm -f tmp.css

print.css:
	@ rm -f assets/css/print.css
	@ cat ${CSS_PRINT} > tmp.css
	@ yuicompressor --charset utf-8 --type css -o tmp.css tmp.css
	@ cat vendor.css tmp.css >> assets/css/print.css
	@ rm -f tmp.css

css: vendor.css app.css print.css
	@ rm -f vendor.css

vendor.js:
	@ cat ${JS_VENDOR} > vendor.js
	@ cat ${JS_LANG} >> vendor.js

app.js:
	@ rm -f tmp.js
	@ rm -f assets/js/app.js
	@ cat ${JS_APP} >> tmp.js
	@ yuicompressor --charset utf-8 --type js -o tmp.js tmp.js
	@ cat vendor.js tmp.js >> assets/js/app.js
	@ rm -f tmp.js

js: vendor.js app.js
	@ rm -f vendor.js

archive:
	@ echo "Build archive: version=${version}, destination=${dst}"
	@ rm -rf ${BUILD_DIR}/kanboard ${BUILD_DIR}/kanboard-*.zip
	@ cd ${BUILD_DIR} && git clone --depth 1 -q https://github.com/fguillot/kanboard.git
	@ cd ${BUILD_DIR}/kanboard && composer --prefer-dist --no-dev --optimize-autoloader --quiet install
	@ rm -rf ${BUILD_DIR}/kanboard/data/*
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
	@ rm -rf ${BUILD_DIR}/kanboard/.docker
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
	@ phpunit --coverage-html /tmp/coverage --whitelist app/ -c tests/units.sqlite.xml

test-sqlite:
	@ phpunit -c tests/units.sqlite.xml

test-mysql:
	@ phpunit -c tests/units.mysql.xml

test-postgres:
	@ phpunit -c tests/units.postgres.xml

unittest: test-sqlite test-mysql test-postgres

sql:
	@ pg_dump --schema-only --no-owner --no-privileges --file app/Schema/Sql/postgres.sql kanboard
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

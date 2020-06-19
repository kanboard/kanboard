DOCKER_IMAGE := docker.io/kanboard/kanboard
DOCKER_TAG := master
VERSION := $(shell git rev-parse --short HEAD)

.PHONY: archive test-sqlite test-mysql test-postgres sql \
	docker-image docker-manifest docker-run docker-sh

archive:
	@ echo "Build archive: version=$(VERSION)"
	@ git archive --format=zip --prefix=kanboard/ $(VERSION) -o kanboard-$(VERSION).zip

test-sqlite:
	@ ./vendor/bin/phpunit -c tests/units.sqlite.xml

test-mysql:
	@ ./vendor/bin/phpunit -c tests/units.mysql.xml

test-postgres:
	@ ./vendor/bin/phpunit -c tests/units.postgres.xml

sql:
	@ pg_dump --schema-only --no-owner --no-privileges --quote-all-identifiers -n public --file app/Schema/Sql/postgres.sql kanboard
	@ pg_dump -d kanboard --column-inserts --data-only --table settings >> app/Schema/Sql/postgres.sql
	@ pg_dump -d kanboard --column-inserts --data-only --table links >> app/Schema/Sql/postgres.sql

	@ mysqldump -uroot --quote-names --no-create-db --skip-comments --no-data --single-transaction kanboard | sed 's/ AUTO_INCREMENT=[0-9]*//g' > app/Schema/Sql/mysql.sql
	@ mysqldump -uroot --quote-names --no-create-info --skip-comments --no-set-names kanboard settings >> app/Schema/Sql/mysql.sql
	@ mysqldump -uroot --quote-names --no-create-info --skip-comments --no-set-names kanboard links >> app/Schema/Sql/mysql.sql

	@ let password_hash=`php -r "echo password_hash('admin', PASSWORD_DEFAULT);"` ;\
	echo "INSERT INTO users (username, password, role) VALUES ('admin', '$$password_hash', 'app-admin');" >> app/Schema/Sql/mysql.sql ;\
	echo "INSERT INTO public.users (username, password, role) VALUES ('admin', '$$password_hash', 'app-admin');" >> app/Schema/Sql/postgres.sql

	@ let mysql_version=`echo 'select version from schema_version;' | mysql -N -uroot kanboard` ;\
	echo "INSERT INTO schema_version VALUES ('$$mysql_version');" >> app/Schema/Sql/mysql.sql

	@ let pg_version=`psql -U postgres -A -c 'copy(select version from schema_version) to stdout;' kanboard` ;\
	echo "INSERT INTO public.schema_version VALUES ('$$pg_version');" >> app/Schema/Sql/postgres.sql

	@ grep -v "SET idle_in_transaction_session_timeout = 0;" app/Schema/Sql/postgres.sql > temp && mv temp app/Schema/Sql/postgres.sql

docker-image:
	@ docker build --build-arg VERSION=$(VERSION) -t $(DOCKER_IMAGE):$(DOCKER_TAG) .

docker-manifest:
	for version in $(VERSION) latest; do \
		docker build --build-arg VERSION=$(VERSION) --build-arg BASE_IMAGE_ARCH=amd64 -t $(DOCKER_IMAGE):amd64-$${version} . && \
		docker build --build-arg VERSION=$(VERSION) --build-arg BASE_IMAGE_ARCH=arm32v6 -t $(DOCKER_IMAGE):arm32v6-$${version} . && \
		docker build --build-arg VERSION=$(VERSION) --build-arg BASE_IMAGE_ARCH=arm32v7 -t $(DOCKER_IMAGE):arm32v7-$${version} . && \
		docker build --build-arg VERSION=$(VERSION) --build-arg BASE_IMAGE_ARCH=arm64v8 -t $(DOCKER_IMAGE):arm64v8-$${version} . && \
		docker push $(DOCKER_IMAGE):amd64-$${version} && \
		docker push $(DOCKER_IMAGE):arm32v6-$${version} && \
		docker push $(DOCKER_IMAGE):arm32v7-$${version} && \
		docker push $(DOCKER_IMAGE):arm64v8-$${version} && \
		docker manifest create --amend $(DOCKER_IMAGE):$${version} \
			$(DOCKER_IMAGE):amd64-$${version} \
			$(DOCKER_IMAGE):arm32v6-$${version} \
			$(DOCKER_IMAGE):arm32v7-$${version} \
			$(DOCKER_IMAGE):arm64v8-$${version} && \
		docker manifest annotate $(DOCKER_IMAGE):$${version} \
			$(DOCKER_IMAGE):arm32v6-$${version} --os linux --arch arm --variant v6 && \
		docker manifest annotate $(DOCKER_IMAGE):$${version} \
			$(DOCKER_IMAGE):arm32v7-$${version} --os linux --arch arm --variant v7 && \
		docker manifest annotate $(DOCKER_IMAGE):$${version} \
			$(DOCKER_IMAGE):arm64v8-$${version} --os linux --arch arm64 --variant v8 && \
		docker manifest push --purge $(DOCKER_IMAGE):$${version} ;\
	done

docker-run:
	@ docker run --rm --name=kanboard -p 80:80 -p 443:443 $(DOCKER_IMAGE):$(DOCKER_TAG)

docker-sh:
	@ docker exec -ti kanboard bash

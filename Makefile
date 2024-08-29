###########################
# Docker                  #
###########################
start:
	docker compose up --remove-orphans -d;

up: start

stop:
	docker compose stop;

down:
	docker compose down;

restart:
	docker compose restart;

bash:
	docker compose exec app /bin/sh;

###########################
# Local development       #
###########################
init: init-db init-rr

# Install dolt database
init-db:
	if [ ! -f "dolt" ]; then \
		vendor/bin/dload get dolt;\
		chmod +x dolt;\
	fi

	if [ ! -d ".db" ]; then \
		mkdir -p .db; \
		chmod 0777 -R .db; \
		./dolt --data-dir=.db sql -q "create database llm;"; \
	fi

# Install RoadRunner
init-rr:
	if [ ! -f "rr" ]; then \
		vendor/bin/rr get;\
	fi

clear-cache:
	rm -rf runtime/cache;

init: init-db init-rr

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

init-rr:
	if [ ! -f "rr" ]; then \
		vendor/bin/rr get;\
	fi

clear-cache:
	rm -rf runtime/cache;

start:
	./rr serve

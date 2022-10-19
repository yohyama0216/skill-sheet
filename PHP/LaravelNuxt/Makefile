ps:
	@docker-compose ps

up:
	@docker-compose up -d
	@make ps

stop:
	@docker-compose stop
	@make ps

migrate:
	@docker-compose exec backend php artisan migrate
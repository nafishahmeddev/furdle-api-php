# Makefile for managing Docker containers

.PHONY: build up down restart logs shell clean

# Build the Docker images
build:
	docker-compose build

# Start the containers
up:
	docker-compose up -d

# Stop the containers
down:
	docker-compose down

# Restart the containers
restart:
	docker-compose restart

# View logs
logs:
	docker-compose logs -f

# Access the app container shell
shell:
	docker-compose exec app bash

# Clean up containers and volumes
clean:
	docker-compose down -v --remove-orphans
	docker system prune -f
# Ticket Application

This is a ticket management application built with Symfony. The application uses Docker for containerization and includes services for the application, MySQL database, and phpMyAdmin.

## Prerequisites

- Docker
- Docker Compose

## Getting Started

fill in .env file with
DATABASE_URL="mysql://root@127.0.0.1:3306/ticket?serverVersion=8.0.32&charset=utf8mb4"
API_TOKEN="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhZG1pbiIsImlhdCI6MTUxNjIzOTAyMn0.-9J1j7"
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'

Install Composer Dependencies
Access the application container:
COMMAND:    docker exec -it ticket_app bash

Install Composer dependencies:
COMMAND: composer install
Set Up the Database
Create the database schema:
COMMAND: php bin/console doctrine:schema:update --force
Run data fixtures:
COMMAND: php bin/console doctrine:fixtures:load
Running Tests
Run PHPUnit tests:
COMMAND: php bin/phpunits

### Clone the Repository

```bash
git clone https://github.com/apalibrk/ticket-api.git
cd ticket-app

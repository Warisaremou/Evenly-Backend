# EVENLY BACKEND

### Description

The platform allows organizers to create and manage events while making it easier for users to book event tickets. It aims to simplify event management and provide a smooth experience for participants.

### RUN THE PROJECT LOCALY

Clone the project

```bash
  git clone https://github.com/Warisaremou/Evenly-Backend.git
```

Go to the project directory

```bash
  cd Evenly-Backend
```

Install dependencies

```bash
  composer install
```

Fill `POSTGRES_PASSWORD:` and `POSTGRES_DB:` in docker-compose.yml file with your database password and name

Run additional container to setup database and adminer

```bash
  docker compose up -d
```

Add .env file

```bash
  cp .env.example .env
```

Fill `DB_DATABASE=` and `DB_PASSWORD=` in .env file with database name and password from docker-compose.yml file

Generate Application Key

```bash
  php artisan key:generate
```

Run server

```bash
  php artisan serve
```

Run migrations

```bash
  php artisan migrate
```

Run seeders

```bash
  php artisan db:seed
```

## Links

-   Swagger: <http://localhost:3000/docs>
-   Adminer (client for DB): <http://localhost:8080>

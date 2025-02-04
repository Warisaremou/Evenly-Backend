# EVENLY BACKEND

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

Add .env file

```bash
  cp .env.example .env
```

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

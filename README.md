# Cliceat Services

New Services Laravel for project Cliceat

## Installation

```sh
$ git clone https://github.com/BorisGautier/cliceat-services.git services
$ cd services
$ cp .env.example .env
```

-   edit & add DB & Email infos in .env



```
$ docker-compose up -d
$ docker exec -it cliceat-services bash
$ composer update
```

```
$ php artisan key:generate
$ php artisan passport:install
$ php artisan storage:link
$ exit
```

-   Add authorization in docker

```
go to services folder
$ chown -R www-data:www-data *
```


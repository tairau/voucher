# VOUCHER

## How to use:

```shell
cp .env.example .env
# Configure .env if you not use Docker

# Docker compose
docker-compose build php
docker-compose run --rm php composer install
docker-compose run --rm php php bin/console make:table 
docker-compose run --rm php php bin/console seed:vouchers

docker-compose up -d
```

URL: http://localhost:8001

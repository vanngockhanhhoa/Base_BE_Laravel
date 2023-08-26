## Prerequisite
- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Composer](https://getcomposer.org/download/)
- Php@8.1
- Install node module to build api doc and mock server
```
npm install -g aglio
npm install -g drakov

```

#
## Setup

1. In folder of project, run follow commands:

```
composer install
OR
./vendor/bin/sail up
```

2. Generate key for jwt
```
php artisan jwt:secret
```

#

## Build api doc
```
aglio -i blueprint/api.apib -o blueprint/api.html
```

in folder blueprint, open api.html to see doc

```
drakov -f blueprint/api.apib -p 3001
```
now you can request mock api with localhost:3001

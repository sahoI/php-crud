# php-crud
勉強会用

## 流れの確認
1. Dockerのセットアップ
2. phpのコード作成
3. 動作確認
4. 質問

## Dockerがインストールされているか確認
$ docker-compose -v
> docker-compose version 1.25.5, build 8a1c60f6


## Dockerのセットアップ
### ファイル構成
```
php-crud
├── docker-compose.yml
├── docker
│   ├── mysql
│   │   └── data
│   │       ├── ...
│   ├── nginx
│   │   └── nginx.conf
│   └── php
│       ├── Dockerfile
│       └── php.ini
└── server
    ├── index.php
    └── task.php
```
### ファイル作成
$ touch docker-compose.yml
$ mkdir -p docker/nginx
$ touch docker/nginx/nginx.conf
$ mkdir -p docker/php
$ touch docker/php/Dockerfile
$ touch docker/php/php.ini
$ mkdir server
$ touch server/index.php

### ファイルのセットアップ
### Docker起動

$ docker exec -it db bash
###

docker-compose.yml
```
version: '3'
services:
  nginx:
    image: nginx:latest
    container_name: nginx
    ports:
      - 8080:80
    volumes:
      - ./docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf
      - ./server:/var/www/html
    depends_on:
      - php

  php:
    container_name: php
    build: ./docker/php
    volumes:
      - ./server:/var/www/html
    depends_on:
      - db

  db:
    image: mysql:5.7
    container_name: db-host
    ports:
      - 3306:3306
    volumes:
      - ./docker/mysql/data:/var/lib/mysql

  phpmyadmin:
    image: phpmyadmin/phpmyadmin:latest
    ports:
      - 8888:80
    depends_on:
      - db
```


$ docker exec -i -t mysql bash 

\# mysql -uroot -p(root)

```
create database if not exists taskDB;
use taskDB;
create table if not exists task (
    id int unsigned not null primary key auto_increment,
    title varchar(32) not null,
)
```

## 動作確認


- curl http://localhost:8080/api
- curl http://localhost:8080/api/3
- curl -X POST -d 'title=*test*' http://localhost:8080/api
- curl -X PUT -d 'title=*updated*' http://localhost:8080/api/:id
- curl -X DELETE http://localhost:8080/api/:id

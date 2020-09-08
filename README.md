# php-crud
phpで簡単にcrud書いてみました。

## 手順
1. Dockerのセットアップ
    1. ファイル構成
    2. ファイル作成
    3. ファイルのセットアップ
    4. Docker起動(1)
    5. DB接続
    6. Docker起動(2)
    7. DB作成
3. コード作成
4. 動作確認
5. 質問

## 0. phpについて
phpはサーバー上で動くプログラミング言語

**サーバーが必要**

図?

## 1. Dockerのセットアップ
### 1. ファイル構成

php-crudはgitからpullしてもいいし、新しく作ってもいい
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
│       ├── init.sql
│       └── php.ini
├── server
│   ├── index.php
│   └── task.php
└── setup.sh
```
### 2. ファイル作成

- 以下のコマンドでファイル構造を作成する
    - setup.shは必要なディレクトリとファイルを生成するスクリプト

`$ sh setup.sh`

※ *docker/mysql*はmysqlをbuildしたときに自動生成

---
↓ setup.shの中身
```
touch docker-compose.yml 

mkdir -p docker/nginx 
touch docker/nginx/nginx.conf

mkdir -p docker/php 
touch docker/php/Dockerfile 
touch docker/php/php.ini 

mkdir server 
touch server/index.php
```
---

### 3. ファイルのセットアップ

1. docker-compose.yml
- dbは不要なのでコメントアウトしている


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
      
  # db:
  #   image: mysql:5.7
  #   container_name: db
  #   ports:
  #     - 3306:3306
  #   volumes:
  #     - ./docker/mysql/*:/docker-entrypoint-initdb.d
  #   environment:
  #     - MYSQL_ROOT_PASSWORD=root
  #     - MYSQL_DATABASE=task
```

2. nginx/nginx.conf
```
server {
    listen 80;

    root  /var/www/html;
    index index.php index.html;

    access_log /var/log/nginx/access.log;
    error_log  /var/log/nginx/error.log;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include       fastcgi_params;
    }
}
```

3. php/Dockerfile

- 3,4行目はdbの設定なためとりあえずコメントアウト

```
FROM php:7.4-fpm
COPY php.ini /usr/local/etc/php/
# COPY init.sql /docker-entrypoint-initdb.d/
# RUN docker-php-ext-install pdo_mysql
```

5. php/php.ini
```
date.timezone = "Asia/Tokyo"
```

6. server/index.php
```
<?php phpinfo() ?>
```

### 4. Docker起動(1)
- まずbuildする
- docker-compose.ymlのphpの項目でbuildするように設定しているため

```
$ docker-compose build
$ docker-compose up

バックグラウンドで実行 ($ docker-compose up -d)
```

- うまく行けば画面にphpinfo()が表示される

### 5. DB接続


1. php/init.sql

```
use task;
create table if not exists task (
    id int unsigned not null primary key auto_increment,
    title varchar(32) not null
);
```

2. docker-compose.yml

- コメントアウトを外す

3. php/Dockerfile

- コメントアウトを外す


### 6. Docker起動(2)
(1)と同じ
- まずbuildする
- docker-compose.ymlのphpの項目でbuildするように設定しているため

```
$ docker-compose build
$ docker-compose up

バックグラウンドで実行 ($ docker-compose up -d)
```

### 7. DB作成
- dockerのコンテナ内に入る
- mysqlを起動する
 
```
$ docker exec -i -t db bash 

\# mysql -uroot -p(root)
```

- 下記のコードを実行する
```
use task;
create table if not exists task (
    id int unsigned not null primary key auto_increment,
    title varchar(32) not null
);
```


## 3. コード作成

1. index.php

- `phpinfo();`の部分はコメントアウトする。
- 下記のコードをコピペする。

    - 補足
    - > $db = new PDO('mysql:host=db;dbname=task;charset=utf8mb4', 'root', 'root');
    - mysqlとphpの接続を行っている
    - Dockerでの場合、hostの部分が要注意
    - 「db」はymlファイルで定義したservicesの「db」を使用している
```
<?php
// phpinfo();
require "task.php";
$db = new PDO('mysql:host=db;dbname=task;charset=utf8mb4', 'root', 'root');
$httpdmethod = $_SERVER['REQUEST_METHOD'];

switch ($httpdmethod) {
    case 'GET':
        if (preg_match('/[api]\/[0-9]/', $_SERVER["REQUEST_URI"])) {
            $id = preg_replace('/[^0-9]/', '', $_SERVER["REQUEST_URI"]);
            getTask($db, $id);
        } elseif ($_SERVER["REQUEST_URI"] == '/api') {
            getTasks($db);
        }
        break;
    case 'POST':
        createTask($db);
        break;
    case 'PUT':
        $id = preg_replace('/[^0-9]/', '', $_SERVER["REQUEST_URI"]);
        updateTask($db, $id);
        break;
    case 'DELETE':
        $id = preg_replace('/[^0-9]/', '', $_SERVER["REQUEST_URI"]);
        deleteTask($db, $id);
        break;
}
?>
```

3. task.php
- 下記のコードをコピペする。
```
<?php
    function getTasks($db) {
        $sql = "SELECT * FROM task";
        $result = $db->query($sql);
        foreach ($result as $task) {
            $id = $task["id"];
            $array[$id]["title"] = $task['title'];
        }
        if($array) {
            echo json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
        } else {
            echo "データがありません。" . PHP_EOL;
        }
    }
    function createTask($db) {
        if(!empty($_POST)) {
            $title = $_POST['title'];

            try {
                $sql = "INSERT INTO task (title) VALUES ('$title')";
                $result = $db->query($sql);
                if ($result) {
                    unset($_POST);
                    echo "作成しました。" . PHP_EOL;
                } else {
                    echo "作成できませんでした" . PHP_EOL;
                }
            } catch(PDOException $e){
               echo $e->getMessage();
            }
        }
    }
    function getTask($db,$id) {
        $sql = "SELECT * FROM task WHERE id=$id";
        $result = $db->query($sql);
        if($result) {
            foreach ($result as $task) {
                $id = $task["id"];
                $array[$id]["title"] = $task['title'];
                echo json_encode($array, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            }
        } else {
            echo "データがありません。" . PHP_EOL;
        } 
    }

    function updateTask($db,$id) {
        parse_str(file_get_contents('php://input'), $put_param);
        if(!empty($put_param))
        {
            $title = $put_param['title'];
            try {
                $sql = "UPDATE task SET title='$title' WHERE id=$id";
                $result = $db->query($sql);
                echo '変更しました' . PHP_EOL;
            } catch (PDOException $e) {
                echo "変更できませんでした" . PHP_EOL;
                echo $e->getMessage();
                exit;
            }
        }
    }

    function deleteTask($db,$id) {
        if(!empty($id)) {
            $sql = "DELETE FROM task WHERE id=$id";
            $result = $db->query($sql);
            echo '消去できました。' . PHP_EOL;
        } else {
            echo "消去できませんでした。" . PHP_EOL;
            exit;
        }
    }
?>
```

## 4. 動作確認
- curl http://localhost:8080/api
- curl http://localhost:8080/api/2
- curl -X POST -d 'title=*test*' http://localhost:8080/api
- curl -X PUT -d 'title=*updated*' http://localhost:8080/api/:id
- curl -X DELETE http://localhost:8080/api/:id


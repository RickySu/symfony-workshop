建立 Entities 以及 CRUD
============================

1) 設定資料庫連線參數
-------------------

編輯 app/config/parameters.yml

```yml
parameters:
    database_driver: pdo_mysql
    database_host: 127.0.0.1
    database_port: null
    database_name: symfony
    database_user: root
    database_password: symfonyworkshop
```

設定好相關的連線參數，例如資料庫的帳號密碼以及資料庫名稱。

2) 建立資料庫
-------------

如果已經建立好資料庫，可以跳過此段。
透過下面的指令，Symfony 會根據你的連線設定建立好資料庫。

    app/console doctrine:database:create

3) 建立 Entity
-------------

在 Doctrine 中 Entity 就是對應資料庫中的 Table。
首先建立 Category Entity。

    app/console doctrine:generate:entity

接著 Symfony 會開始進行一連串的問答

```
First, you need to give the entity name you want to generate.
You must use the shortcut notation like AcmeBlogBundle:Post.

The Entity shortcut name: WorkshopBackendBundle:Category

Determine the format to use for the mapping information.

Configuration format (yml, xml, php, or annotation) [annotation]:
```

The Entity shortcut name 就填入 *WorkshopBackendBundle:Category*

註解方式就採用預設的 annotation



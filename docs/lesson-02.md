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

接著 Symfony 會開始詢問資料表的欄位

```
Available types: array, simple_array, json_array, object,
boolean, integer, smallint, bigint, string, text, datetime, datetimetz,
date, time, decimal, float, blob, guid.

New field name (press <return> to stop adding fields):
```

依序填入名稱以及欄位的資料類型，填完後直接按下 <return> (enter) 按鍵，結束對話。

這裡我們就只建立一個欄位 name 類型是 string 長度 255。

```
New field name (press <return> to stop adding fields): name
Field type [string]:
Field length [255]:
```

接著 Symfony 會詢問是否要建立一個空的 repository class。這裡選擇 yes。
完成 Category Entity 的建立。

```
Do you want to generate an empty repository class [no]? yes


  Summary before generation


You are going to generate a "WorkshopBackendBundle:Category" Doctrine2 entity
using the "annotation" format.

Do you confirm generation [yes]?


  Entity generation


Generating the entity code: OK


  You can now start using the generated code!
```


Unit Test And Functional Test
==============================

1) PHPUnit 安裝
---------------

在 PHP 上要作單元測試就不得不提到 PHPUnit 這套測試框架。

最快速簡單的安裝方法就是直接下載打包好的 PHAR

```
wget https://phar.phpunit.de/phpunit.phar
php phpunit.phar
```

或是透過 Composer

```JSON
{
    "require-dev": {
        "phpunit/phpunit": "3.7.*"
    }
}
```

透過 PEAR

```
pear config-set auto_discover 1
pear install pear.phpunit.de/PHPUnit
```


初始化專案以及建立前後台 Bundle
============================

1) 初始化專案
-------------

Symfony 的初始化相當簡單，可以採用 [Composer][0] 的方式進行或是直接下載。

### 使用 Composer 安裝 (*官方推薦方式*)###

如果你還沒有安裝 Composer ，可以過下列方式進行安裝。

    curl -s http://getcomposer.org/installer | php

接著使用  Composer 的 create-project 功能就行了

    composer.phar create-project symfony/framework-standard-edition path/to/install

path/to/install 就是你的專案擺放目錄。例如  ~/php/symfony-workshop

### 直接下載壓縮檔 ###

首先到 [Symfony下載頁面][1]，選擇最新的版本，按下 DOWNLOAD NOW 就對了。
記得，不要下載到 without vendors ，不然還得再透過 composer 安裝所有需要的套件。
解開壓縮檔後會得到一個 Symfony 的目錄，自己 rename 成專案名稱吧。

在命令列底下要解壓縮

    tar zxvf Symfony_Standard_Vendors_2.3.6.tgz



[0]:    https://getcomposer.org/
[1]:    http://symfony.com/download

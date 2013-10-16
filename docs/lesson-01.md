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

2) 建立前後台 Bundle
------------------

接下來我們要開啟 terminal，並切換到專案根目錄

    cd ~/php/symfony-workshop

接著開啟 Symfony 的 console 工具

    app/console -s

這時候 Symfony 的 console 工具會進入互動模式，要離開互動模式只要按下 Ctrl+D 就會離開。

首先建立前台 Bundle

    app/console generate:bundle

接著 Symfony 會開始詢問一連串的問題

```
Bundle namespace: Workshop/Bundle/FrontendBundle
Bundle name [WorkshopFrontendBundle]: (使用默認值)
Target directory [/home/symfony/php/symfony/src]: (使用默認值)
Configuration format (yml, xml, php, or annotation): annotation
Do you want to generate the whole directory structure [no]? yes
Do you confirm generation [yes]? (使用默認值)
Confirm automatic update of your Kernel [yes]? (使用默認值)
Confirm automatic update of the Routing [yes]? (使用默認值)
```

這時候就建立好前台 Bundle

接著再來建立後台 Bundle

    app/console generate:bundle

接著 Symfony 會開始詢問一連串的問題

```
Bundle namespace: Workshop/Bundle/BackendBundle
Bundle name [WorkshopFrontendBundle]: (使用默認值)
Target directory [/home/symfony/php/symfony/src]: (使用默認值)
Configuration format (yml, xml, php, or annotation): annotation
Do you want to generate the whole directory structure [no]? yes
Do you confirm generation [yes]? (使用默認值)
Confirm automatic update of your Kernel [yes]? (使用默認值)
Confirm automatic update of the Routing [yes]? (使用默認值)
```

接下來我們得進行一個 assets:install 的動作

    app/console assets:install web --symlink --relative

接著就可以開啟內建的 web server 觀看歡迎畫面了

    app/console server:run

預設的首頁網址就在

    http://localhost:8000/

再來要編輯 app/config/routing.yml

```yml
workshop_backend:
    resource: "@WorkshopBackendBundle/Controller/"
    type:     annotation
    prefix:   /admin

workshop_frontend:
    resource: "@WorkshopFrontendBundle/Controller/"
    type:     annotation
    prefix:   
```

把後台的網址設為 /admin/xxxx

連上這個網址就可以看到後台預設的頁面

    http://localhost:8000/admin/hello/ricky

後台的 Controller 為 

src/Workshop/Bundle/BackendBundle/Controller/DefaultController.php
```php
<?php

namespace Workshop\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
}
```

預設的 View

src/Workshop/Bundle/BackendBundle/Resources/views/Default/index.html.twig

```twig
Hello {{ name }}!
```

[0]:    https://getcomposer.org/
[1]:    http://symfony.com/download

使用 FOSUserBundle 打造後台帳號權限管理
====================================

1) 安裝 FOSUserBundle
---------------------

```
composer.phar require friendsofsymfony/user-bundle
Please provide a version constraint for the friendsofsymfony/user-bundle requirement: *
```

我們只要透過 Composer 就可以輕鬆的安裝好 FOSUserBundle

2) 建立 User Entity
--------------------

接著我們要建立 User Entity。

```
app/console doctrine:generate:entity


  Welcome to the Doctrine2 entity generator



This command helps you generate Doctrine2 entities.

First, you need to give the entity name you want to generate.
You must use the shortcut notation like AcmeBlogBundle:Post.

The Entity shortcut name: WorkshopBackendBundle:User

Determine the format to use for the mapping information.

Configuration format (yml, xml, php, or annotation) [annotation]:

Instead of starting with a blank entity, you can add some fields now.
Note that the primary key will be added automatically (named id).

Available types: array, simple_array, json_array, object,
boolean, integer, smallint, bigint, string, text, datetime, datetimetz,
date, time, decimal, float, blob, guid.

New field name (press <return> to stop adding fields):

Do you want to generate an empty repository class [no]?


  Summary before generation


You are going to generate a "WorkshopBackendBundle:User" Doctrine2 entity
using the "annotation" format.

Do you confirm generation [yes]?


  Entity generation


Generating the entity code: OK


  You can now start using the generated code!

```

在這我們直接建立一個空的 WorkshopBackendBundle:User Entity。

接著我們要將 User Entity 繼承自 FOSUserBundle 提供的 Entity。

編輯 src/Workshop/Bundle/BackendBundle/Entity/User.php

```php
<?php

namespace Workshop\Bundle\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
```

注意 $id 必須設為 protected。(因為 BaseUser 的 $id 是 protected)

3) Enable FOSUserBundle
------------------------

接著編輯 app/config/AppKernel.php

```php
<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            //...加入 FOSUserBundle
            new FOS\UserBundle\FOSUserBundle(),
        );
    }
}
```

3) 設定 FOSUserBundle 參數
-------------------------

編輯 app/config/config.yml 加入

```yaml
fos_user:
    db_driver:        orm
    user_class:       WorkshopBackendBundle:User
    firewall_name:    secured_area
```

由於 FOSUserBundle 支援相當多的 DB 例如 MongoDB, Doctrine ORM, Propel ...
目前我們使用 Doctrine ORM, 因此 db_driver 設為 orm。

user_class 則是我們 User Entity的名稱。

這邊可以使用簡寫的 WorkshopBackendBundle:User 或是完整的的 Class 名稱
Workshop\Bundle\BackendBundle\Entity\User

firewall_name 暫時使用 secured_area，後面會在來修改。

4) 指定密碼加密方式
-----------------

編輯 app/config/security.yml

```yml
security:
    encoders:
        FOS\UserBundle\Model\UserInterface:              sha1
```

FOSUserBundle 提供了多種加密方式，例如 md5, sha1, sha256, sha512 ...

5) 更新資料庫
------------

```
app/console doctrine:schema:update --force
```

6) 建立帳號
----------

FOSUserBundle 提供了一個命令列工具用來建立帳號。

```
app/console fos:user:create
Please choose a username:ricky
Please choose an email:ricky@example.com
Please choose a password:12345
Created user ricky
```

7) 賦予角色
----------

預設情況 FOSUserBundle 會給予每個帳號 ROLE_USER 的基本角色。

這邊我們就賦予一個後台管理員的角色 ROLE_ADMIN。

```
app/console fos:user:promote
Please choose a username:ricky
Please choose a role:ROLE_ADMIN
Role "ROLE_ADMIN" has been added to user "ricky"
```

8) 設定防火牆規則
----------------

Symfony 的安全設定都集中在 app/config/security.yml

```yml
security:
    encoders:
        FOS\UserBundle\Model\UserInterface:              sha1

    role_hierarchy:
        ROLE_ADMIN:        ROLE_USER

    providers:
        admin:
            id:            fos_user.user_provider.username

    firewalls:
        dev:
            pattern:       ^/(_(profiler|wdt)|css|images|js)/
            security:      false

        admin:
            form_login:
                login_path:     fos_user_security_login
                check_path:     fos_user_security_check
            logout:
                path:            fos_user_security_logout
                target:          @BackendHome
            anonymous:           true

    access_control:
        - { path: ^/admin/login,  roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/,       roles: ROLE_ADMIN }
```

接著要把 FOSUserBundle security 相關的 Routing 加入

編輯 app/config/routing.yml

```yml
workshop_backend:
    resource: "@WorkshopBackendBundle/Controller/"
    type:     annotation
    prefix:   /admin

workshop_frontend:
    resource: "@WorkshopFrontendBundle/Controller/"
    type:     annotation
    prefix:   /

fos_user_security:
    resource: "@FOSUserBundle/Resources/config/routing/security.xml"
    prefix: /admin
```
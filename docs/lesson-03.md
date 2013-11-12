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

9) 建立 User 管理 CRUD
---------------------

```
app/console doctrine:generate:crud


  Welcome to the Doctrine2 CRUD generator



This command helps you generate CRUD controllers and templates.

First, you need to give the entity for which you want to generate a CRUD.
You can give an entity that does not exist yet and the wizard will help
you defining it.

You must use the shortcut notation like AcmeBlogBundle:Post.

The Entity shortcut name: WorkshopBackendBundle:User

By default, the generator creates two actions: list and show.
You can also ask it to generate "write" actions: new, update, and delete.

Do you want to generate the "write" actions [no]? yes

Determine the format to use for the generated CRUD.

Configuration format (yml, xml, php, or annotation) [annotation]:

Determine the routes prefix (all the routes will be "mounted" under this
prefix: /prefix/, /prefix/new, ...).

Routes prefix [/user]:


  Summary before generation


You are going to generate a CRUD controller for "WorkshopBackendBundle:User"
using the "annotation" format.

Do you confirm generation [yes]?


  CRUD generation


Generating the CRUD code: OK
Generating the Form code: OK


  You can now start using the generated code!

```

將 User 管理的路徑加入選單中

編輯 src/Workshop/Bundle/BackendBundle/Resources/views/Common/_header.html.twig

```jinja
<header role="banner" class="navbar navbar-inverse navbar-fixed-top bs-docs-nav">
    <div class="container col-md-10">
        <div class="navbar-header">
            <a class="navbar-brand" href="http://symfony.com/"><img style="height:18px;" src="{{asset('apple-touch-icon.png')}}" /></a>
        </div>

        <div class="navbar-header">
            <a class="navbar-brand" href="{{path('@BackendHome')}}">Home</a>
        </div>
        <nav role="navigation" class="collapse navbar-collapse bs-navbar-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href="{{path('category')}}">Category</a>
                </li>
                <li>
                    <a href="{{path('post')}}">Post</a>
                </li>
                <li>
                    <a href="{{path('user')}}">User</a>
                </li>
            </ul>
        </nav>
    </div>
    <div class="container col-md-2">
        <nav role="navigation" class="collapse navbar-collapse bs-navbar-collapse">
            <ul class="nav navbar-nav pull-right">
                <li>
                    <a href="{{path('fos_user_security_logout')}}">Logout {{app.user.username}}</a>
                </li>
            </ul>
        </nav>
    </div>
</header>
```

因為 FOSUserBundle 提供了相當多的欄位，但是有些欄位是 private 因此必須隱藏掉，不要在列表中顯示出來。

編輯 src/Workshop/Bundle/BackendBundle/Resources/views/User/index.html.twig

```jinja
{%extends "WorkshopBackendBundle:Layout:bootstrapLayout.html.twig"%}

{% block main %}
<h1>User list</h1>

<table class="records_list table table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Username</th>
            <th>Email</th>
            <th>Enabled</th>
            <th>Lastlogin</th>
            <th>Locked</th>
            <th>Expired</th>
            <th>Roles</th>
            <th>Credentialsexpired</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        {% for entity in entities %}
        <tr>
            <td>{{ entity.id }}</td>
            <td><a href="{{ path('user_show', { 'id': entity.id }) }}">{{ entity.username }}</a></td>
            <td>{{ entity.email }}</td>
            <td>{{ entity.enabled }}</td>
            <td>{% if entity.lastLogin %}{{ entity.lastLogin|date('Y-m-d H:i:s') }}{% endif %}</td>
            <td>{{ entity.locked }}</td>
            <td>{{ entity.expired }}</td>
            <td>
                <ul class="list">
                {%for role in entity.roles %}
                    <li>{{role}}</li>
                {%endfor%}
                </ul>
            </td>
            <td>{{ entity.credentialsExpired }}</td>
            <td>
                <ul>
                    <li>
                        <a href="{{ path('user_show', { 'id': entity.id }) }}">show</a>
                    </li>
                    <li>
                        <a href="{{ path('user_edit', { 'id': entity.id }) }}">edit</a>
                    </li>
                </ul>
            </td>
        </tr>
        {% endfor %}
    </tbody>
</table>

<ul class="list list-inline">
    <li>
        <a href="{{ path('user_new') }}">
            Create a new entry
        </a>
    </li>
</ul>
{% endblock %}

```

編輯 src/Workshop/Bundle/BackendBundle/Resources/views/User/show.html.twig

```jinja
{%extends "WorkshopBackendBundle:Layout:bootstrapLayout.html.twig"%}

{% block main %}
<h1>User</h1>

<table class="record_properties table table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ entity.id }}</td>
        </tr>
        <tr>
            <th>Username</th>
            <td>{{ entity.username }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ entity.email }}</td>
        </tr>
        <tr>
            <th>Enabled</th>
            <td>{{ entity.enabled }}</td>
        </tr>
        <tr>
            <th>Lastlogin</th>
            <td>{{ entity.lastLogin|date('Y-m-d H:i:s') }}</td>
        </tr>
        <tr>
            <th>Locked</th>
            <td>{{ entity.locked }}</td>
        </tr>
        <tr>
            <th>Expired</th>
            <td>{{ entity.expired }}</td>
        </tr>
        <tr>
            <th>Roles</th>
            <td>
                <ul class="list">
                {%for role in entity.roles %}
                    <li>{{role}}</li>
                {%endfor%}
                </ul>
            </td>
        </tr>
        <tr>
            <th>Credentialsexpired</th>
            <td>{{ entity.credentialsExpired }}</td>
        </tr>
    </tbody>
</table>

<ul class="record_actions list list-inline">
    <li>
        <a href="{{ path('user') }}">
            Back to the list
        </a>
    </li>
    <li>
        <a href="{{ path('user_edit', { 'id': entity.id }) }}">
            Edit
        </a>
    </li>
    <li>{{ form(delete_form) }}</li>
</ul>
{% endblock %}
```

更新 User Form

編輯 src/Workshop/Bundle/BackendBundle/Form/UserType.php

```php
<?php
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('enabled', 'checkbox', array('required' => false))
            ->add('plain_password', 'password', array(
                'required' => false,
            ))
            ->add('plain_password', 'repeated', array(
                'type' => 'password',
                'first_options' => array(
                    'label' => 'Password',
                ),
                'second_options' => array(
                    'label' => 'Password Again',
                ),
                'required' => false,
            ))
            ->add('locked', 'checkbox', array('required' => false))
            ->add('expired', 'checkbox', array('required' => false))
            ->add('roles', 'choice', array(
                'choices' => array(
                    'ROLE_USER' => 'Normal User',
                    'ROLE_ADMIN' => 'Backend User',
                    'ROLE_POST' => 'Post Admin',
                    'ROLE_CATEGORY' => 'Category Admin',
                    'ROLE_SUPER_ADMIN' => 'Super Admin',
                ),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ))
            ->add('credentialsExpired', 'checkbox', array('required' => false))
        ;
    }

}

```

更新 Controller, 改用 userManager 更新 user。

編輯 src/Workshop/Bundle/BackendBundle/Controller/UserController.php

```php
/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
   /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="user_update")
     * @Method("PUT")
     * @Template("WorkshopBackendBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WorkshopBackendBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            /* @var $userManager \FOS\UserBundle\Doctrine\UserManager */
            $userManager->updateUser($entity);
            //$em->flush();

            return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
}
```
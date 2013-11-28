打造前台文章分類內容檢視
====================================

1) 建立基本 Sidebar Layout
--------------------------

首先建立一個 Sidebar Layout

src/Workshop/Bundle/FrontendBundle/Resources/views/Layout/sidebarLayout.html.twig

```jinja
{%extends "WorkshopBackendBundle:Layout:bootstrapLayout.html.twig"%}

{%block header%}{%include "WorkshopFrontendBundle:Common:_header.html.twig"%}{%endblock%}
{%block footer%}{%include "WorkshopFrontendBundle:Common:_footer.html.twig"%}{%endblock%}

{%block main%}
<div class="row">
    <div class="col-md-3" style="border: 1px solid;height: 300px;">sidebar</div>
    <div class="col-md-9" role="main">{%block content%}{%endblock%}</div>
</div>
{%endblock%}
```

接著設定好前台的 header 跟 footer

src/Workshop/Bundle/FrontendBundle/Resources/views/Common/_header.html.twig

```jinja
<header role="header">
    <div class="page-header">
        <h1>Example page header <small>Subtext for header</small></h1>
    </div>
</header>
```

src/Workshop/Bundle/FrontendBundle/Resources/views/Common/_footer.html.twig

```jinja
<footer role="footer" class="bs-footer">
    <div class="container text-center">
        This is footer.
    </div>
</footer>
```

2) 建立預設首頁
-------------

設定預設首頁的 Controller Action

src/Workshop/Bundle/FrontendBundle/Controller/DefaultController.php

```php
class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
```

設定對應的 View

src/Workshop/Bundle/FrontendBundle/Resources/views/Default/index.html.twig

```jinja
{%extends "WorkshopFrontendBundle:Layout:sidebarLayout.html.twig"%}

{% block content %}
{%include "WorkshopFrontendBundle:Post:_list.html.twig" with {posts: posts}%}
{% endblock %}
```

3) 首頁顯示目前所有文章的列表
--------------------------

透過 Doctrine Entity Manager 讀出所有文章。

src/Workshop/Bundle/FrontendBundle/Controller/DefaultController.php

```php
class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('WorkshopBackendBundle:Post')
                ->findBy(array(), array('updatedAt' => 'desc'));
        return array('posts'=>$posts);
    }
}
```

將 posts 傳遞到 view 中

src/Workshop/Bundle/FrontendBundle/Resources/views/Default/index.html.twig

```jinja
{%extends "WorkshopFrontendBundle:Layout:sidebarLayout.html.twig"%}

{% block content %}
    {%for post in posts%}
    <div class="page-header">
        <h1>{{post.subject}} <small>{{post.createdAt|date('Y-m-d H:i:s')}}</small></h1>
    </div>
    <p>{{post.content}}</p>
    {%endfor%}
{% endblock %}
```

為了方便共用文章列表，我們將文章列表抽出獨立成一個 partial view。

src/Workshop/Bundle/FrontendBundle/Resources/views/Post/_list.html.twig

```jinja
{%for post in posts%}
<div class="page-header">
    <h1>{{post.subject}} <small>{{post.createdAt|date('Y-m-d H:i:s')}}</small></h1>
</div>
<p>{{post.content}}</p>
{%endfor%}
```

重新修改首頁的樣板，套用 partial view。

src/Workshop/Bundle/FrontendBundle/Resources/views/Default/index.html.twig

```jinja
{%extends "WorkshopFrontendBundle:Layout:sidebarLayout.html.twig"%}

{% block content %}
{%include "WorkshopFrontendBundle:Post:_list.html.twig" with {posts: posts}%}
{% endblock %}
```

4) 設定 Sidebar 內容，顯示所有的分類目錄
-------------------------------------

建立一個 Category Controller

src/Workshop/Bundle/FrontendBundle/Controller/CategoryController.php

```php
<?php

namespace Workshop\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Workshop\Bundle\BackendBundle\Entity;

/**
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Template()
     */
    public function _categoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('WorkshopBackendBundle:Category')
                ->createQueryBuilder('c')
                ->orderBy('c.id', 'asc')
                ->getQuery()
                ->getResult();
        return array('categories' => $categories);
    }
}
```

指定好一個 action 讀出目前所有的目錄。

設定對應的 view

src/Workshop/Bundle/FrontendBundle/Resources/views/Category/_category.html.twig

```jinja
<ul style="max-width: 300px;" class="nav nav-pills nav-stacked">
    {%for category in categories%}
    <li><a href="">{{category.name}}</a></li>
    {%endfor%}
</ul>
```

修改 Sidebar Layout，將目錄列表的內容 redner 出來。

src/Workshop/Bundle/FrontendBundle/Resources/views/Layout/sidebarLayout.html.twig

```jinja
{%extends "WorkshopBackendBundle:Layout:bootstrapLayout.html.twig"%}
{%block header%}{%include "WorkshopFrontendBundle:Common:_header.html.twig"%}{%endblock%}
{%block footer%}{%include "WorkshopFrontendBundle:Common:_footer.html.twig"%}{%endblock%}

{%block main%}
<div class="row">
    <div class="col-md-3">{%render(controller("WorkshopFrontendBundle:Category:_category", {currentCategory: currentCategory}))%}</div>
    <div class="col-md-9" role="main">{%block content%}{%endblock%}</div>
</div>
{%endblock%}
```

5) 建立目錄檢視
--------------

src/Workshop/Bundle/FrontendBundle/Controller/CategoryController.php

補上一個 categoryAction

```php
/**
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/{id}-{name}", name="@categoyIndex")
     * @Template()
     */
    public function indexAction(Entity\Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('WorkshopBackendBundle:Post')
                ->findBy(array('category' => $category), array('updatedAt' => 'desc'));
        return array('category' => $category, 'posts' => $posts);
    }
}
```

設定對應的 view

src/Workshop/Bundle/FrontendBundle/Resources/views/Category/index.html.twig

```jinja
{%extends "WorkshopFrontendBundle:Layout:sidebarLayout.html.twig"%}

{% block content %}
{%include "WorkshopFrontendBundle:Post:_list.html.twig" with {posts: posts}%}
{% endblock %}
```

目錄列表(Sidebar)補上 Link

src/Workshop/Bundle/FrontendBundle/Resources/views/Category/_category.html.twig

```jinja
<ul style="max-width: 300px;" class="nav nav-pills nav-stacked">
    {%for category in categories%}
    <li><a href="{{path('@categoyIndex', {id: category.id, name: category.name})}}">{{category.name}}</a></li>
    {%endfor%}
</ul>
```

6) 讓目前檢視中的 Category Highlight
----------------------------------

src/Workshop/Bundle/FrontendBundle/Resources/views/Category/index.html.twig

```jinja
{%extends "WorkshopFrontendBundle:Layout:sidebarLayout.html.twig"%}
{%set currentCategory = category%}
{% block content %}
{%include "WorkshopFrontendBundle:Post:_list.html.twig" with {posts: posts}%}
{% endblock %}
```

指定一個 global 的樣板變數 currentCategory

修改 Layout
將 currentCategory 傳遞給 Category:_categoryAction。

src/Workshop/Bundle/FrontendBundle/Resources/views/Layout/sidebarLayout.html.twig

```jinja
{%if currentCategory is not defined%}
{%set currentCategory = null%}
{%endif%}
{%extends "WorkshopBackendBundle:Layout:bootstrapLayout.html.twig"%}
{%block header%}{%include "WorkshopFrontendBundle:Common:_header.html.twig"%}{%endblock%}
{%block footer%}{%include "WorkshopFrontendBundle:Common:_footer.html.twig"%}{%endblock%}

{%block main%}
<div class="row">
    <div class="col-md-3">{%render(controller("WorkshopFrontendBundle:Category:_category", {currentCategory: currentCategory}))%}</div>
    <div class="col-md-9" role="main">{%block content%}{%endblock%}</div>
</div>
{%endblock%}
```

修改 Category:_categoryAction，接收 currentCategory 參數，並傳遞給對應的 view。

src/Workshop/Bundle/FrontendBundle/Controller/CategoryController.php

```php
/**
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Template()
     */
    public function _categoryAction()
    {
        $currentCategory = $this->getRequest()->get('currentCategory');
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('WorkshopBackendBundle:Category')
                ->createQueryBuilder('c')
                ->orderBy('c.id', 'asc')
                ->getQuery()
                ->getResult();
        return array('categories' => $categories, 'currentCategory' => $currentCategory);
    }
}
```

修改 view。

src/Workshop/Bundle/FrontendBundle/Resources/views/Category/_category.html.twig

```jinja
<ul style="max-width: 300px;" class="nav nav-pills nav-stacked">
    {%for category in categories%}
    <li{%if currentCategory and currentCategory.id == category.id%} class="active"{%endif%}><a href="{{path('@categoyIndex', {id: category.id, name: category.name})}}">{{category.name}}</a></li>
    {%endfor%}
</ul>
```

7) 加入檢視文章內容
-----------------

建立 Post Controller。

src/Workshop/Bundle/FrontendBundle/Controller/PostController.php

```php
<?php

namespace Workshop\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Workshop\Bundle\BackendBundle\Entity;

/**
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * @Route("/{id}-{subject}.html", name="@postView")
     * @Template()
     */
    public function viewAction(Entity\Post $post)
    {
        return array('post' => $post);
    }
}
```

編輯對應的 view。

src/Workshop/Bundle/FrontendBundle/Resources/views/Post/view.html.twig

```jinja
{%extends "WorkshopFrontendBundle:Layout:sidebarLayout.html.twig"%}

{% block content %}
<div class="page-header">
    <a href="{{path('@postView', {id: post.id, subject: post.subject})}}"><h1>{{post.subject}} <small>{{post.createdAt|date('Y-m-d H:i:s')}}</small></h1></a>
</div>
<p>{{post.content|nl2br}}</p>
```

幫文章列表的 partial view 補上連結

src/Workshop/Bundle/FrontendBundle/Resources/views/Post/_list.html.twig

```jinja
{%for post in posts%}
<div class="page-header">
    <a href="{{path('@postView', {id: post.id, subject: post.subject})}}"><h1>{{post.subject}} <small>{{post.createdAt|date('Y-m-d H:i:s')}}</small></h1></a>
</div>
<p>{{post.content}}</p>
{%endfor%}
```


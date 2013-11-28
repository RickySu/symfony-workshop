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

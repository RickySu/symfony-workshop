Assetic Filters
===============

1) 後台加入上傳圖片功能
---------------------

修改 Post Entity

編輯 src/Workshop/Bundle/BackendBundle/Entity/Post.php

```php
<?php

namespace Workshop\Bundle\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    //...

    /**
     *
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;


    protected function getUploadRootDir()
    {
        return realpath(__DIR__.'/../../../../../web').'/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/images';
    }

    public function getWebPath()
    {
        if($this->filename === null){
            return null;
        }

        return $this->getUploadDir().'/'.$this->filename;
    }

    public function getAbsolutePath()
    {
        if($this->filename === null){
            return null;
        }
        return $this->getUploadRootDir().'/'.$this->filename;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload()
    {
        if($this->file === null){
            return;
        }

        if(!file_exists($this->getUploadRootDir())){
            mkdir($this->getUploadRootDir(), 0777, true);
        }

        $this->filename = "{$this->getId()}.{$this->getFile()->guessExtension()}";

        $this->getFile()->move($this->getUploadRootDir(), $this->filename);

        $this->setFile(null);
    }

    //...
}
```

加入 filename 以及 setFile , getFile, upload methods。

同步資料庫

```
app/console doctrine:schema:update --force
```

修改 form 加入檔案上傳欄位

編輯 src/Workshop/Bundle/FrontendBundle/Form/PostType.php

```php
class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject')
            ->add('content')
            ->add('category')
            ->add('file', 'file', array(
                'constraints' => array(
                    new Constraints\Image(array(
                        'maxSize' => 6000000
                    ))
                )
            ))
        ;
    }
}
```

補上一個 file 欄位，設定篩選條件必須是圖片，而且檔案大小不能超過 6MB。

更新對應的 template。

src/Workshop/Bundle/BackendBundle/Resources/views/Post/edit.html.twig

```jinja
{%extends "WorkshopBackendBundle:Layout:bootstrapLayout.html.twig"%}

{% block main %}
<h1>Post edit</h1>
    <img class="thumbnail" src="{{asset(entity.webPath)}}" />
    {{ form(edit_form) }}

<ul class="record_actions list list-inline">
    <li>
        <a href="{{ path('post') }}" class="btn btn-link">
            Back to the list
        </a>
    </li>
    <li>{{ form(delete_form) }}</li>
</ul>
{% endblock %}
```

src/Workshop/Bundle/BackendBundle/Resources/views/Post/show.html.twig

```jinja
{%extends "WorkshopBackendBundle:Layout:bootstrapLayout.html.twig"%}

{% block main %}
<h1>Post</h1>

<table class="record_properties table table-striped">
    <tbody>
        <tr>
            <th>Id</th>
            <td>{{ entity.id }}</td>
        </tr>
        <tr>
            <th>Category</th>
            <td>{%if entity.category%}{{ entity.category.name }}{%endif%}</td>
        </tr>
        <tr>
            <th>Subject</th>
            <td>{{ entity.subject }}</td>
        </tr>
        <tr>
            <th>Content</th>
            <td>{{ entity.content }}</td>
        </tr>
        <tr>
            <th>Createdat</th>
            <td>{{ entity.createdAt|date('Y-m-d H:i:s') }}</td>
        </tr>
        <tr>
            <th>Updatedat</th>
            <td>{{ entity.updatedAt|date('Y-m-d H:i:s') }}</td>
        </tr>
        <tr>
            <th>Image</th>
            <td><img src="{{ asset(entity.webPath) }}" /></td>
        </tr>
    </tbody>
</table>

<ul class="record_actions list list-inline">
    <li>
        <a href="{{ path('post') }}" class="btn btn-link">
            Back to the list
        </a>
    </li>
    <li>
        <a href="{{ path('post_edit', { 'id': entity.id }) }}" class="btn btn-default">
            <i class="glyphicon glyphicon-edit"></i> Edit
        </a>
    </li>
    <li>{{ form(delete_form) }}</li>
</ul>
{% endblock %}
```

更新 Controller

src/Workshop/Bundle/BackendBundle/Controller/PostController.php

```php
<?php

/**
 * Post controller.
 *
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * Creates a new Post entity.
     *
     * @Route("/", name="post_create")
     * @Method("POST")
     * @Template("WorkshopBackendBundle:Post:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Post();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $entity->upload();    //補上 upload
            $em->flush();

            return $this->redirect($this->generateUrl('post_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing Post entity.
     *
     * @Route("/{id}", name="post_update")
     * @Method("PUT")
     * @Template("WorkshopBackendBundle:Post:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WorkshopBackendBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->upload();    //補上 upload
            $em->flush();
            return $this->redirect($this->generateUrl('post_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

}
```

更新前台文章列表

src/Workshop/Bundle/FrontendBundle/Resources/views/Post/_list.html.twig

```jinja
{%for post in posts%}
<div class="page-header">
    <a href="{{path('@postView', {id: post.id, subject: post.subject})}}"><h1>{{post.subject}} <small>{{post.createdAt|date('Y-m-d H:i:s')}}</small></h1></a>
</div>
<div class="container">
    <img src="{{asset(post.webPath)}}" class="col-md-3"/>
    <p class="col-md-9">{{post.content}}</p>
</div>
{%endfor%}
```

更新前台文章內容

src/Workshop/Bundle/FrontendBundle/Resources/views/Post/view.html.twig

```jinja
{%extends "WorkshopFrontendBundle:Layout:sidebarLayout.html.twig"%}
{% form_theme form 'WorkshopBackendBundle:Common:_form.html.twig' %}

{% block content %}
<div class="page-header">
    <a href="{{path('@postView', {id: post.id, subject: post.subject})}}"><h1>{{post.subject}} <small>{{post.createdAt|date('Y-m-d H:i:s')}}</small></h1></a>
</div>
<img src="{{asset(post.webPath)}}" class="thumbnail"/>
<p>{{post.content|nl2br}}</p>
<h2>Comments</h2>
{{render(controller("WorkshopFrontendBundle:Post:_comments", {id: post.id}))}}
<h2>Add a Comment</h2>
{{form(form)}}
{% endblock %}
```

2) 啟用 Assets Version
----------------------

app/config.yml

```yml
framework:
    templating:
        assets_version:            %assets_version%
        assets_version_format:     %%s?v%%s
        assets_base_urls:
                    -               '//localhost:8000'
                    -               '//127.0.0.1:8000'
```

app/parameters.yml

```yml
parameters:
    assets_version:        10
```

將目前版號設成第 10 版

3) 啟用 Assetic Filter
----------------------

app/config.yml

```yml
# Assetic Configuration
assetic:
    debug:          %kernel.debug%
    use_controller: false
    #bundles:        [ ]
    #java: /usr/bin/java
    node:                "/usr/bin/node"
    filters:
        cssrewrite: ~
        stylus:
            apply_to:     "\.stylus$"
        coffee:
            bin:           "/usr/bin/coffee"
            apply_to:      "\.coffee$"
        #closure:
        #    jar: %kernel.root_dir%/Resources/java/compiler.jar
        #yui_css:
        #    jar: %kernel.root_dir%/Resources/java/yuicompressor-2.4.7.jar
```

注意記得要將 bundles:    [] 註解掉

app/config_prod.yml

```yml
assetic:
    filters:
        uglifycss:
            bin:         /usr/bin/uglifycss
            apply_to:   "\.css$"
        uglifyjs2:
            bin:         /usr/bin/uglifyjs2
            apply_to:   "\.js$"
```

在 Production 模式中，啟用 JS 以及 CSS 壓縮合併功能

如果 node 以及 uglifycss, uglifyjs 不是安裝在預設的 /usr/bin/ 底下，
記得修改 assetic -> node: 以及 各自的 assetic -> filters -> * -> bin: 的設定。


安裝 Node.js

```
sudo apt-add-repository ppa:chris-lea/node.js
sudo apt-get update
sudo apt-get install nodejs nodejs-dev
```

安裝 uglifyjs2

```
npm -g install uglify-js2
```

安裝 uglifycss

```
npm -g install uglifycss
```

安裝 stylus

```
npm -g install stylus
```

安裝 coffeescript

```
npm -g install coffee-script
```

[下載 Bootstrap][0]

將 css, js, font 解開後放到 src/Workshop/Bundle/FrontendBundle/Resources/public

[下載 jQuery][1]

將 jquery-1.x.min.js 放到 src/Workshop/Bundle/FrontendBundle/Resources/public/js/

編輯 src/Workshop/Bundle/BackendBundle/Resources/views/Layout/bootstrapLayout.html.twig

```jinja
{%extends "::base.html.twig"%}

{%if edit_form is defined%}
    {% form_theme edit_form 'WorkshopBackendBundle:Common:_form.html.twig' %}
{%endif%}
{%if delete_form is defined%}
    {% form_theme delete_form 'WorkshopBackendBundle:Common:_form.html.twig' %}
{%endif%}
{%if form is defined%}
    {% form_theme form 'WorkshopBackendBundle:Common:_form.html.twig' %}
{%endif%}

{%block javascripts%}
{{parent()}}
{% javascripts
    'bundles/workshopbackend/js/jquery.min.js'
    'bundles/workshopbackend/js/bootstrap.min.js'
    output='js/backendbase.js'
%}
<script src="{{ asset_url }}"></script>
{% endjavascripts %}
{%endblock%}

{%block stylesheets%}
{{parent()}}
{% stylesheets
    'bundles/workshopbackend/css/bootstrap.min.css'
    'bundles/workshopbackend/css/bootstrap-theme.min.css'
    'bundles/workshopbackend/css/main.css'
    output='css/backendbase.css'
%}
<link rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
{%endblock%}

{%block body%}
    {%block header%}{%include "WorkshopBackendBundle:Common:_header.html.twig"%}{%endblock%}
    <div class="container main-content">{%block main%}{%endblock%}</div>
    {%block footer%}{%include "WorkshopBackendBundle:Common:_footer.html.twig"%}{%endblock%}
{%endblock%}
```

編輯 src/Workshop/Bundle/FrontentendBundle/Resources/views/Layout/bootstrapLayout.html.twig

```jinja
{%extends "::base.html.twig"%}

{%if edit_form is defined%}
    {% form_theme edit_form 'WorkshopBackendBundle:Common:_form.html.twig' %}
{%endif%}
{%if delete_form is defined%}
    {% form_theme delete_form 'WorkshopBackendBundle:Common:_form.html.twig' %}
{%endif%}
{%if form is defined%}
    {% form_theme form 'WorkshopBackendBundle:Common:_form.html.twig' %}
{%endif%}

{%block javascripts%}
{{parent()}}
{% javascripts
    'bundles/workshopbackend/js/jquery.min.js'
    'bundles/workshopbackend/js/bootstrap.min.js'
    output='js/frontendbase.js'
%}
<script src="{{ asset_url }}"></script>
{% endjavascripts %}
{%endblock%}

{%block stylesheets%}
{{parent()}}
{% stylesheets
    'bundles/workshopbackend/css/bootstrap.min.css'
    'bundles/workshopbackend/css/bootstrap-theme.min.css'
    'bundles/workshopfrontend/css/main.stylus'
    output='css/frontendbase.css'
%}
<link rel="stylesheet" href="{{ asset_url }}" />
{% endstylesheets %}
{%endblock%}

{%block body%}
    {%block header%}{%include "WorkshopBackendBundle:Common:_header.html.twig"%}{%endblock%}
    <div class="container main-content">{%block main%}{%endblock%}</div>
    {%block footer%}{%include "WorkshopBackendBundle:Common:_footer.html.twig"%}{%endblock%}
{%endblock%}
```

編輯 src/Workshop/Bundle/FrontentendBundle/Resources/views/Layout/sidebarLayout.html.twig

```jinja
{%if currentCategory is not defined%}
{%set currentCategory = null%}
{%endif%}
{%extends "WorkshopFrontendBundle:Layout:bootstrapLayout.html.twig"%}
{%block header%}{%include "WorkshopFrontendBundle:Common:_header.html.twig"%}{%endblock%}
{%block footer%}{%include "WorkshopFrontendBundle:Common:_footer.html.twig"%}{%endblock%}

{%block main%}
<div class="row">
    <div class="col-md-3">{%render(controller("WorkshopFrontendBundle:Category:_category", {currentCategory: currentCategory}))%}</div>
    <div class="col-md-9" role="main">{%block content%}{%endblock%}</div>
</div>
{%endblock%}
```

編輯 src/Workshop/Bundle/FrontentendBundle/Resources/css/main.stylus

```yml
.main-content
    margin:        50px 0 0 0
    min-height:    300px

.bs-footer
    background-color:  #FAFAFA
    border-bottom:     1px solid #E5E5E5
    border-top:        1px solid #FFFFFF
    color:             #777777
    padding:           15px 20px
```

重新打包 css 跟 js

```
app/console assetic:dump
```

如果是要正式發行 (Production mode)

```
app/console assetic:dump -e prod
```

讓 symfony 自動監視，一有異動就執行 dump 動作 (注意: 這個動作只能在開發模式中執行)

```
app/console assetic:dump --watch --period 1
```

symfony 就會每隔1秒鐘去檢查所有的 js, css 以及 template，並且執行 dump。

4) 啟用 LiveReload
------------------

安裝 ruby

```
sudo apt-add-repository ppa:brightbox/ruby-ng-experimental
sudo apt-get update
sudo apt-get install ruby2.0 ruby2.0-dev
```

安裝 guard-livereload

```
sudo gem install guard-livereload
```

安裝 browser extension

[http://feedback.livereload.com/knowledgebase/articles/86242-how-do-i-install-and-use-the-browser-extensions-][3]

根據對應的瀏覽器安裝外掛

編輯 Guardfile 位於專案根目錄

```ruby
# A sample Guardfile
# More info at https://github.com/guard/guard#readme

guard 'livereload' do
  watch(%r{web/.+\.(css|js|html)})
end
```

開始執行 live-reload

```
guard

17:52:36 - INFO - Guard is using NotifySend to send notifications.
17:52:36 - INFO - Guard is using TerminalTitle to send notifications.
17:52:36 - INFO - LiveReload is waiting for a browser to connect.
17:52:36 - INFO - Guard is now watching at '/home/symfony/php/symfony-workshop'
[1] guard(main)>
```

[0]:  http://getbootstrap.com/
[1]:  http://jquery.com/download/
[3]:  http://feedback.livereload.com/knowledgebase/articles/86242-how-do-i-install-and-use-the-browser-extensions-
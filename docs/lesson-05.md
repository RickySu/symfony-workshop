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

app/config.yml

```yml
# Assetic Configuration
assetic:
    debug:          %kernel.debug%
    use_controller: false
    #bundles:        [ ]
    #java: /usr/bin/java
    filters:
        cssrewrite: ~
        stylus:
            apply_to:   "\.stylus$"
        coffee:
            apply_to:   "\.coffee$"
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
            apply_to:   "\.css$"
        uglifyjs2:
            bin:         /usr/bin/uglifyjs2
            apply_to:   "\.js$"
```

在 Production 模式中，啟用 JS 以及 CSS 壓縮合併功能

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


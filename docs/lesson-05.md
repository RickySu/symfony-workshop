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
<?php

class File extends ModelBase
{
    public $file_id;
    public $document_id;
    public $target = '';
    public $target_id;
    public $sort = 0;
    public $file_name;
    public $file_type;
    public $file_path;
    public $full_path;
    public $raw_name;
    public $original_name;
    public $extension;
    public $file_size;
    public $is_image = 0;
    public $width = 0;
    public $height = 0;
    public $caption;
    public $is_used = 0;
    public $exif;
    public $created_at = 0;
    public $updated_at = 0;
    public $thumbnail_list;

    public $base_url = '/';

    public function initialize() {
        parent::initialize();
    }


    public function onConstruct()
    {
//        $this->base_url = Context::getPostypeUrl()."/";
        $this->base_url = "/";
    }

    public function getFileUrl() {
        return $this->base_url.$this->getFilePath();
    }
    public function getFilePath()
    {
        return $this->full_path;
    }

    public function getThumbnail($width = 0, $height = 0) {
        return $this->base_url.$this->getImagePath($width, $height);
    }

    /**
     * @param int $width
     * @param int $height
     * @return null|string
     * @deprecated
     */
    public function getImagePath($width = 0, $height = 0)
    {
        if(!$this->is_image) {
            return null;
        }

        if($width > 0 or $height > 0) {

            $thumbnail_list = unserialize($this->thumbnail_list);
            if($height == 0) {
                $height = (int)(($width * $this->height) / $this->width);
            }

            if($width > $this->width and $height > 0) {
                $height = round($this->width * $height / $width);
                $width = $this->width;
            }

            if($width == $this->width and $height == $this->height) {
                return $this->getFilePath();
            }

            $key = "t".$width."x".$height;
            if(!isset($thumbnail_list[$key])) {
                $thumbnail_list[$key] = $this->getResizedImage($width, $height);
                $this->thumbnail_list = serialize($thumbnail_list);
                $this->save();
            }
            return $thumbnail_list[$key];
        }
        return $this->getFilePath();
    }

    public static function uploadByUrl($url, $target = '', $target_id = 0) {

        $file_path = "files/".date("Y/m/d/H/i/");
        $path = parse_url($url, PHP_URL_PATH);
        $original_name = substr(strrchr($path,'/'),1);
        $extension = substr(strrchr($path,'.'),1);

        $raw_name = md5($url.rand(0,9999999));
        $file_name = $raw_name.".".$extension;
        $full_path = $file_path.$file_name;

        error_log("file directory make");
        if(!file_exists($file_path)) {
            @mkdir($file_path, 0777, true);
        }
        error_log("file directory maked");

        $result = file_put_contents($full_path, file_get_contents($url));
        $type = null;
        $width = 0;
        $height = 0;
        $is_image = 0;
        $exif = null;
        if($result) {
            $size = getimagesize($full_path);
            if($size) {
                $width = $size[0];
                $height = $size[1];
                $is_image = 1;
                $type = $size['mime'];
            }
        }

        if($type == 'image/jpeg') {
            $exif = @exif_read_data($full_path);
        }

        $obj = new File();
        $obj->original_name = $original_name;
        $obj->raw_name = $raw_name;
        $obj->file_name = $file_name;
        $obj->file_type = $type;
        $obj->file_path = $file_path;
        $obj->full_path = $file_path.$file_name;
        $obj->extension = $extension;
        $obj->target = $target;
        $obj->target_id = $target_id;
        $obj->file_size = filesize($full_path);
        if($obj->file_size<1) {
            return null;
        }
        $obj->created_at = time();
        $obj->updated_at = time();

        $obj->width = $width;
        $obj->height = $height;
        $obj->is_image = $is_image;
        $obj->exif = serialize($exif);

        if($obj->save()) {
            return File::findFirst($obj->file_id);
        }
        else {
            error_log("error : ".$obj->getMessages());
            Log::e($obj->getFirstMessage());
        }

        return null;
    }
    /**
     * @param \Phalcon\Http\Request\File|\Phalcon\Http\Request\FileInterface|SplFileInfo $file
     * @param string $target
     * @param int $target_id
     * @return File
     */
    public static function upload($file, $target='user', $target_id = 0)
    {
        if(is_string($file)) {
            error_log("uploadByUrl start");
            return File::uploadByUrl($file, $target='', $target_id);
            error_log("uploadByUrl end");
        }

        $file_path = "files/".date("Y/m/d/H/i/");
        $original_name = $file->getName();
        $extension = $file->getExtension();
        $raw_name = md5($file->getName().rand(0,9999999));
        $file_name = $raw_name.".".$extension;
        $type = $file->getRealType();
        $full_path = $file_path.$file_name;
        $size = getimagesize($file->getTempName());

        $width = 0;
        $height = 0;
        $is_image = 0;
        $exif = null;

        if($size) {
            $width = $size[0];
            $height = $size[1];
            $is_image = 1;
        }

        if(!file_exists($file_path)) {
            @mkdir($file_path, 0777, true);
        }

        if(is_dir($file_path)) {
            if($type == 'image/jpeg') {
                $exif = @exif_read_data($file->getTempName());
            }

            $orientation = null;
            if($exif and isset($exif["Orientation"])) {
                $rotate_degree = 0;
                $orientation = $exif["Orientation"];
                if (6 == $orientation) {
                    $rotate_degree = 90;
                } elseif (3 == $orientation) {
                    $rotate_degree = 180;
                } elseif (8 == $orientation) {
                    $rotate_degree = 270;
                }
                $image = new \Phalcon\Image\Adapter\Imagick($file->getTempName());
                $img = $image->getInternalImInstance();
                $img->stripImage(); // 사진의 exif 정보를 삭제함. 안그러면 모바일에서 자동 회전이 동작
                $image->rotate($rotate_degree)->save(__DIR__."/../../public/".$full_path);
            } else {
                if(!$file->moveTo(__DIR__."/../../public/".$full_path)) {
                    Log::e("파일 이동 오류:".$file->getFilename());
                }
            }
        }
        else {
            return null;
        }
        $obj = new File();
        $obj->original_name = $original_name;
        $obj->raw_name = $raw_name;
        $obj->file_name = $file_name;
        $obj->file_type = $type;
        $obj->file_path = $file_path;
        $obj->full_path = $file_path.$file_name;
        $obj->extension = $extension;
        $obj->target = $target;
        $obj->target_id = $target_id;
        $obj->file_size = $file->getSize();
        $obj->created_at = time();
        $obj->updated_at = time();

        $obj->width = $width;
        $obj->height = $height;
        $obj->is_image = $is_image;
        $obj->exif = serialize($exif);

        if($obj->save()) {
            return File::findFirst($obj->file_id);
        }
        else {
            Log::e($obj->getFirstMessage());
            return false;
        }
    }

    public function beforeDelete()
    {
//        $key = "file.".$this->target.".".$this->target_id;
//        ModelBase::deleteCache($key);
//
//        $key = 'file.'.$this->file_id;
//        ModelBase::deleteCache($key);

        if(file_exists($this->full_path)) {
            @unlink($this->full_path);
        }

        $thumbnail_list = unserialize($this->thumbnail_list);
        if($thumbnail_list) {
            foreach($thumbnail_list as $val) {
                if(file_exists($val)) {
                    @unlink($val);
                }
            }
        }
    }

    public function save($data = null, $whiteList = null)
    {
        if(!$this->file_id) {
            $this->created_at = time();
        }
        $this->updated_at = time();

//        $key = "file.".$this->target.".".$this->target_id;
//        ModelBase::deleteCache($key);
//
//        $key = 'file.'.$this->file_id;
//        ModelBase::deleteCache($key);

        return parent::save($data, $whiteList);
    }

    public static function findByFileId($file_id) {

        $file = File::findFirst($file_id);
        return $file;

    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model\ResultsetInterface|File[]
     */
    public static function find($parameters=null) {
        return parent::find($parameters);
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model|File
     */
    public static function findFirst($parameters=null) {

        $key = null;
//        if(is_numeric($parameters)) {
//            $key = 'file.'.$parameters;
//        }
//        if($key) {
//            $file = ModelBase::getCache($key);
//            if($file) {
//                return $file;
//            }
//        }

        $file = parent::findFirst($parameters);
//        if($key) {
//            ModelBase::setCache($key, $file);
//        }
        return $file;
    }

    public static function findFile($target, $target_id) {
        $key = "file.".$target.".".$target_id;
        if($key) {
            $file = ModelBase::getCache($key);
            if($file == 'N') {
                return null;
            }
            else if($file) {
                return $file;
            }
        }

        $file = File::findFirst("target_id = {$target_id} and target='{$target}'");
        if($key and $file) {
            ModelBase::setCache($key, $file);
        }
        else if($key) {
            ModelBase::setCache($key, 'N');
        }
        return $file;
    }

    private function getResizedImage($width=0, $height=0) {
        if(is_file($this->full_path)) {
            $image = null;
            if($this->file_type == 'image/gif') {
                $image = new \Phalcon\Image\Adapter\Gd($this->full_path);
            } else {
                $image = new \Phalcon\Image\Adapter\Imagick($this->full_path);
            }

            if(!$image) {
                error_log("섬네일 지원 안함\n");
                return null;
            }
            if($width==0 and $height==0) {
                // 가로, 세로 값이 둘다 0일 경우 이미지 변환 연산을 하지 않음.
                return $this->full_path;
            }
            if($width==0) {
                $width = ceil($height * $image->getWidth() / $image->getHeight());
            }
            else if($height==0) {
                $height = ceil($width * $image->getHeight() / $image->getWidth());
            }
            if($width==0 or $height==0) {
                return null;
            }
            if($image->getWidth()/$image->getHeight() > $width / $height) {
                // 가로 자름
                $image->crop(ceil($image->getHeight() * $width / $height), $image->getHeight(), ($image->getWidth()-ceil($image->getHeight() * $width / $height)) / 2);
            }
            else {
                // 세로 자름
                $image->crop($image->getWidth(), ceil($image->getWidth() * $height / $width), null, ($image->getHeight() - ceil($image->getWidth() * $height / $width)) / 2);
            }
            $new_file_path = $this->file_path."{$width}/";
            if(!is_dir($new_file_path)) {
                @mkdir($new_file_path);
            }
            $new_full_path = $new_file_path."{$width}x{$height}_".$this->file_name;
            if($image->resize($width, $height)->save(__DIR__."/../../public/".$new_full_path, 90)) {
                return $new_full_path;
            }
            else {
                return null;
            }
        }
        else {
            return null;
        }
    }

    public static function getFilePathByFileId($file_id) {
        $file = File::findFirst($file_id);
        if($file) {
            return $file->getFilePath();
        }
        else {
            return "/assets/img/sample.png";
        }
    }
}
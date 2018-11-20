<?php
/**
 * @Author: [Dzero] <81951882@qq.com>
 * @Date:   2016-01-08 19:41:46
 * @Last Modified by:   [Dzero] <81951882@qq.com>
 * @Last Modified time: 2016-01-08 21:44:14
 */

class Cache
{
    public $path;
    public $type;
    public function __construct($type = 'array', $path = 'Cache/'){
        $dir = APP_PATH.$path.MODULE.'/';
        is_dir($dir) || mkdir($dir, 0755, true);
        $this->path = $dir;
        $this->type = $type;
    }
    public function create($name = null, $data = null){
        if(is_null($name)) return false;
        if($this->type == 'array'){
            file_put_contents($this->path.$name.'.cache.php', serialize($data));
        }elseif($this->type == 'json'){
            return file_put_contents($this->path.$name.'.cache.php', json_encode($data));
        }
    }

    public function get($name = null){
        if(is_null($name)) return false;
        if($this->type == 'array'){
            return unserialize(file_get_contents($this->path.$name.'.cache.php'));
        }elseif($this->type == 'json'){
            return json_decode(file_get_contents($this->path.$name.'.cache.php'));
        }
    }

    public function del($name = null){
        if(is_null($name)) return false;
        $file = $this->path.$name.'.cache.php';
        return is_file($file) && unlink($file);
    }
}
<?php

/**
 * @Author: LDF
 * @Date:   2018-11-18 18:30:41
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-18 18:37:50
 */
class Log {
  static public function create($data = [], $dir = '', $filename = 'log.php') {
    if(empty($data) || !$dir) return;
    is_dir($dir) || mkdir($dir, 0755, true);
    $file = $dir. $filename;
    $res = is_file($file) ? require_once($file) : [];
    array_unshift($res, $data);
    return file_put_contents($file, "<?php \nreturn ". var_export($res, true). ';');
  }
}
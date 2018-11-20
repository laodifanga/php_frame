<?php

/**
 * @Author: LDF
 * @Date:   2018-11-19 11:34:45
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-19 13:00:34
 */
class File {
  static public $files = [];
  static public function get($filename = null) {
    return self::$files;
  }

  static public function load($file = null, $vars = null) {
    if(is_null($file)) return;
    if(is_array($file)) {
      foreach ($file as $f) {
        self::_loadSimpleFile($f, $vars);
      }
      return true;
    }
    return self::_loadSimpleFile($file, $vars);
  }

  static private function _loadSimpleFile($file = null, $vars = null) {
    if(!isset(self::$files[$file]) && is_file($file)) {
      self::$files[$file] = strstr($file, APP_PATH);
      if(!is_null($vars)) {
        extract($vars);
      }
      return require_once($file);
    }
  }
}
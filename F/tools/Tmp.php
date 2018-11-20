<?php

/**
 * @Author: LDF
 * @Date:   2018-11-16 10:03:07
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-16 14:22:57
 */
class Tmp { // TODO 解耦
  static public function clear($name = null, $moudule = 'home') {
    if(is_null($name)) { // 清除所有
      Dir::del(TMP_PATH);
    }
    switch($name) {
      case 'session': // 清除session
        Dir::del(TMP_PATH. $moudule. DS. 'session');
        break;
      default: // 清除所有
        Dir::del(TMP_PATH);
        break;
    }
  }
}
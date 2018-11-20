<?php
/**
 * @Author: LDF
 * @Date:   2018-11-15 17:09:50
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-19 11:44:55
 */
class C {
  public $template_vars = [];
  public function __construct() {}

  // public function __set($name, $val) {
  //   $this->assign($name, $val);
  // }

  // public function __get($name) {
  //   throw new Error($name. '不存在');
  // }

  public function assign($name, $val) {
    $this->template_vars[$name] = $val;
  }

  public function display($tpl = '') {
    $fix = '.'. Config::get('TPL_SUFFIX');
    $tpl = $tpl ? $tpl. $fix : TPL_PATH. A. $fix;
    if(is_file($tpl)) File::load($tpl, $this->template_vars);

    // extract($this->template_vars);
    // if(is_file($tpl)) require_once $tpl;
  }

  public function fetch($tpl = '') {
    return $this->display($tpl);
  }
}
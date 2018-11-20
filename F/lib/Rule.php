<?php

/**
 * @Author: LDF
 * @Date:   2018-11-16 14:29:10
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-16 17:40:12
 */
/**
 * E.g
 * public $rules = [
    'index' => [
      'searchName', // 非必传，会以null存在$this->data中
      'uid'=> true,
      'uname'=> '用户名必填',
      'age' => [
        'require' => true,
        'type' => 'int',
        // 'desc' => '年龄必传',
        'preg' => null,
        'min' => 2,
        'max' => 4,
      ],
      'birth' => [
        'require' => true,
        // 'type' => 'int',
        'desc' => '生日必传',
        'min' => 2,
        'max' => 4,
      ],
      'tel' => [
        'require' => true,
        'preg' => '/\d+/',
        'desc' => '手机号',
      ],
      'url' => [
        'type' => 'url',
        'desc' => '填几个数字看看'
      ]
    ],
  ];
 */
class Rule {
  public static $rules = [];
  public static $code = 9;

  public static function check(&$data = null, $rules = null, $action = null) {
    if(isset($rules[$action])) { // 转换为标准数组
      $rule = $rules[$action];
      foreach ($rule as $name => $val) {
        if(is_int($name)) { // 只有键名 || 非必传项合并到 $_REQUEST $this->data 里
          self::changeToArray($val, ['require' => false]);
          continue;
        }
        is_array($val) ? self::changeToArray($name, $val) : self::changeToArray($name, ['desc' => is_bool($val) ? $name. ' is required.' : $val]);
      }
    }

    self::validate($data);
  }

  // 转换为标准数组
  static private function changeToArray($name = null, $val = null) {
    self::$rules[$name] = array_merge([
      'require' => true,
      'desc' => null,
      'type' => 'string',
      'default' => null,
    ], $val);
  }

  // 标准数组检测
  static private function validate(&$data) {
    // p(self::$rules);
    foreach (self::$rules as $name => $val) {
      if(!isset($data[$name])) { // 未传参数
        if(!$val['require']) { // 非必传项合并到 $_REQUEST $this->data 里
          $data[$name] = Request::set($name, $val['default']);
          continue;
        }
        return Ajax::show(self::$code, is_null($val['desc']) ? $name. ' is required.' : $val['desc']);
      }

      $value = $data[$name];

      if(isset($val['type'])) { // 参数类型
        $valis = [
          'int' => ['/^\d+$/isU', '数字'],
          'email' => ['/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/isU', '邮箱'],
          'phone' => ['/^1\d{10}$/isU', '手机号'],
          'tel' => ['/^((\+?[0-9]{2,4}\-[0-9]{3,4}\-)|([0-9]{3,4}\-))?([0-9]{7,8})(\-[0-9]+)?$/isU', '电话'],
          'url' => ['/^^((https|http|ftp|rtsp|mms)?:\/\/)[^\s]+$/is', '网址'],
        ];
        $type = $val['type'];
        if( isset($valis[$type]) ) {
          !preg_match($valis[$type][0], $value) && Ajax::show(self::$code, $name.'必须是'. $valis[$type][1]);
        }
        switch ($type) {
          case 'array': !is_array($value) && Ajax::show(self::$code, $name.'必须是数组'); break;
        }
      }

      // preg
      if(!empty($val['preg'])) {
        if(!preg_match($val['preg'], $value)) Ajax::show(self::$code, is_null($val['desc']) ? $name. ' do not conform to the rules.' : $val['desc']);
      }

      // min-max
      $value = $val['type'] == 'int' ? $data[$name] : strlen($data[$name]);
      if(isset($val['min'])) {
        $desc = is_null($val['desc']) ? $name. ' >= '. $val['min'] : $val['desc'];
        if( $value < $val['min']) return Ajax::show(self::$code, $desc);
      }
      if(isset($val['max'])) {
        $desc = is_null($val['desc']) ? $name. ' <= '. $val['max'] : $val['desc'];
        if( $value > $val['max']) return Ajax::show(self::$code, $desc);
      }
    }
  }
}
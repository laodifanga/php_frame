<?php

/**
 * @Author: LDF
 * @Date:   2018-11-17 15:47:12
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-19 11:23:50
 */
abstract class Db {
  protected $database;
  protected $table;
  protected $prefix;
  protected $fields = [];
  private $_data = []; //data() 存储数据
  protected $option = [
    'order' => '',
    'limit' => '',
    'field' => '*',
    'where' => '',
    'group' => '',
    'having' => '',
    'join' => '',
    'uuid' => ''
  ];

  abstract function query($sql = '');
  abstract function exec($sql = '');
  abstract function execute($sql = '');

  public function table($tableName = null) {
    if(is_null($tableName)) return;
    $this->database = Config::get('DB_NAME');
    $this->prefix = Config::get('DB_PREFIX');
    $this->table = "`{$this->prefix}{$tableName}`";
    $this->_getAllFields(); // 获取字段和主键
  }

  public function join($tableName = '', $alias = '', $on = '', $join = 'JOIN') {
    $sql = " {$join} `{$this->prefix}{$tableName}`";
    $alias = $alias ? : $tableName;
    $sql .= " AS `{$alias}` ON {$on}";
    $this->option['join'] .= $sql;
    return $this;
  }

  // 别名
  public function alias($name = '') {
    if(strstr($this->table, 'AS')) return $this;
    $this->table = $this->table . " AS `$name`";
    return $this;
  }

  public function select() {
    $sql = "SELECT {$this->option['field']} FROM {$this->table}{$this->option['join']}{$this->option['where']}{$this->option['group']}{$this->option['having']}{$this->option['order']}{$this->option['limit']}";
    $res = $this->query($sql);
    return isset($this->_date) ? $this->_formatDate($res) : $res;
  }

  public function find($id = null) {
    if(!is_null($id)) { // 按照主键查询
      $this->_setPriMap($id);
    }
    if(empty($this->option['where'])) return;
    $sql = "SELECT {$this->option['field']} FROM {$this->table}{$this->option['join']}{$this->option['where']}{$this->option['order']} LIMIT 1";
    $res = $this->query($sql);
    $res = isset($this->_date) ? $this->_formatDate($res) : $res;
    return current($res);
  }

  public function count($field = '*') { // 统计
    return current( current($this->query("SELECT count($field) AS `c` FROM {$this->table}{$this->option['where']}")) );
  }

  public function sum($field = null) { // 统计
    $field = [$field => $field];
    $field = current( $this->filter( $field ) );
    if(empty($field)) return;
    return current( current( $this->query("SELECT sum($field) AS `s` FROM {$this->table}{$this->option['where']}") ) );
  }

  // INSERT INTO table (x,x) VALUES (x,x);
  public function add($data = []) {
    $data = empty($data) ? $this->_data : $data;

    if(! $execData = $this->_getExecData($data)) return $execData;

    return $this->exec("INSERT INTO $this->table ({$execData['field']}) VALUES ({$execData['value']})");
  }

  // INSERT INTO table (x,x) VALUES (x,x),(x,x),(x,x);
  public function addAll($data = []) {
    if(empty($data)) return false;

    if(! $execData = $this->_getArrayExecData($data)) return $execData;

    return $this->exec("INSERT INTO $this->table ({$execData['field']}) VALUES {$execData['value']}");
  }

  // REPLACE INTO table (x,x) VALUES (x,x),(x,x),(x,x);
  public function replaceAll($data = []) {
    if(empty($data)) return false;

    if(! $execData = $this->_getArrayExecData($data)) return $execData;

    return $this->exec("REPLACE INTO $this->table ({$execData['field']}) VALUES {$execData['value']}");
  }

  // UPDATE table SET x=x,x=x WHERE x
  // E.g ['name' => x, 'hot' => ['inc', 2]]
  public function update($data = [], $field = '') {
    $data = $this->filter($data);
    if(empty($this->option['where'])) return;
    if(!$field) {
      if(empty($data)) return;

      foreach ($data as $name => $val) {
        if(is_array($val)) {
          $field .= ','. $this->_getNumField($name, isset($val[1]) ? $val[1] : 1, $val[0] == 'dec' ? '-' : '+');
          continue;
        }
        $field .= ",`{$name}`='{$val}'";
      }
      $field = substr($field, 1);
    }

    return $this->exec("UPDATE {$this->table} SET {$field} {$this->option['where']}");
  }

  // DELETE FROM table WHERE x
  public function delete($id = null) {
    if(!is_null($id)) { // 按照主键查询
      $this->_setPriMap($id);
    }
    return $this->exec( "DELETE FROM {$this->table}{$this->option['where']}" );
  }

  public function inc($field = '', $step = 1) {
    return $this->update([], $this->_getNumField($field, $step));
  }

  public function dec($field = '', $step = 1) {
    return $this->update([], $this->_getNumField($field, $step, '-'));
  }

  public function data($data = []) {
    $this->_data = $data;
    return $this;
  }

  public function uuid($val = '') {
    return $this->_setOption('uuid', $val, $val);
  }

  public function field($val = '') {
    return $this->_setOption('field', $val, $val);
  }

  public function where($val = '') {
    return $this->_setOption('where', " WHERE $val", $val);
  }

  public function order($val = '') {
    return $this->_setOption('order', " ORDER BY $val", $val);
  }

  public function limit($val = '') {
    return $this->_setOption('limit', " LIMIT $val", $val);
  }

  public function group($val = '') {
    return $this->_setOption('group', " GROUP BY $val", $val);
  }

  public function having($val = '') {
    return $this->_setOption('having', " HAVING $val", $val);
  }

  public function filter($data = []) { // 过滤非表中字段
    return array_intersect_key($data, $this->fields); // 以data为主
  }

  protected function resetOption() {  // 恢复选项
    foreach ($this->option as $name => $v) {
      $this->option[$name] = $name == 'field' ? '*' : '';
    }
  }

  private function _setOption($name = '', $val = '', $raw = '') { // 设置选项
    if( !isset($this->option[$name]) ) return $this;
    $raw && $this->option[$name] = $val;
    return $this;
  }

  private function _formatDate($data) { // 时间戳转换
    if(empty($this->_date)) return $data;
    $res = [];
    foreach ($data as $d) {
      foreach ($this->_date as $k => $v) {
        $name = is_string($k) ? $k : $v;
        $format = is_string($k) ? $v : 'Y-m-d H:i:s';
        if(isset($d[ $name ])) {
          $d["_{$name}"] = $d[$name] ? date($format, $d[$name]) : null;
        }
      }
      $res[] = $d;
    }
    return $res;
  }

  private function _getNumField($field = '', $step = 1, $sign = '+') { // 获取inc dec field
    return "`{$field}`=`{$field}`${sign}{$step}";
  }

  private function _getAllFields() { // 获取所有字段和主键
    $data = $this->db->query("DESC $this->table");
    foreach ($data as $d) {
      $Field = $d['Field'];
      $Key = $d['Key'];
      $this->fields[$Field] = $Key;
    }
  }

  private function _setPriMap($id) { // 设置主键查找条件
    $pris = '';
    foreach ($this->fields as $f => $v) {
      if($v == 'PRI') {
        $pris .= " OR `{$f}`=$id";
      }
    }
    if(!empty($pris)) {
      $pris = substr($pris, 4);
      $this->option['where'] = " WHERE ({$pris})";
    }
  }

  private function _getArrayExecData($data) { // 二维获取数据
    $val = '';
    $field = '';
    foreach ($data as $d) {
      $execData = $this->_getExecData($d);
      if(!$execData) return false;
      if(!$field) {
        $field = $execData['field'];
      }
      $val .= ',('. $execData['value']. ')';
    }
    $val = substr($val, 1);
    return ['field' => $field, 'value' => $val];
  }

  private function _getExecData($data) { // 获取exec数据
    $data = $this->filter($data);

    if(empty($data) || empty(array_keys($data))) return false;

    $key = '`'. implode('`,`', array_keys($data)). '`';
    $val = '"'. implode('","', array_values($data)). '"';

    if($uuid = $this->option['uuid']) {
      $key = $key. ",`$uuid`";
      $val = $val.",UUID()";
    }

    return ['field' => $key, 'value' => $val];
  }
}
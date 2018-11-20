<?php

/**
 * @Author: LDF
 * @Date:   2018-11-17 15:44:22
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-19 11:25:05
 */
class M extends Db{
  public $logs = [];
  public $data = [];
  protected $db;
  public function __construct($table = null) {
    $className = get_class($this);
    if(is_null($table) && __CLASS__ !== $className) { // 以modal文件名为表名
      $table = strtolower($className);
    }
    $this->db = $this->getDb();
    $this->table($table);

    $this->data = Request::get();
  }

  private function getDb() {
    $driver = strtoupper( Config::get('DB_DRIVER') );
    if($driver === 'PDO') {
      return _PDO::getInstance();
    }
  }

  public function query($sql = ''){
    $this->logs[] = $sql;
    $this->resetOption();
    return $this->db->query($sql);
  }

  public function exec($sql = ''){
    $this->log($sql);
    return $this->db->exec($sql);
  }

  public function execute($sql = '', $data = []) {
    $this->log($sql);
    return $this->db->execute($sql, $data);
  }

  public function begin() {
    return $this->db->begin();
  }

  public function commit() {
    return $this->db->commit();
  }

  public function rollBack() {
    return $this->db->rollBack();
  }

  private function log($sql) {
    $this->logs[] = $sql;
    $this->resetOption();

    if(!Config::get('SQL_LOG')) return;

    // 写入tmp文件夹
    $logDir = TMP_PATH. M. DS. 'sql'. DS . date('Y-m-d'). DS;
    $ip = $_SERVER['REMOTE_ADDR'];
    $data = ['atime' => time(), 'date' => date('Y-m-d H:i:s'), 'sql' => $sql, 'ip' => $ip];
    Log::create($data, $logDir, 'log.php');
  }
}
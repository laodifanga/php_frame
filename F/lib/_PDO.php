<?php

/**
 * @Author: LDF
 * @Date:   2018-11-17 15:59:49
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-19 16:27:25
 */
class _PDO implements DbInterface {
  static protected $link;
  protected $conn;

  public function __construct() {
    $host = Config::get('DB_HOST');
    $dbname = Config::get('DB_NAME');
    $port = Config::get('DB_PORT');
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname}";
    $this->connect($dsn, Config::get('DB_USER'), Config::get('DB_PWD'));
  }

  static public function getInstance() {
    return self::$link = self::$link ? : new self;
  }

  public function connect($dsn = null, $user = null, $pwd = null) {
    try {
      $this->conn = new PDO($dsn, $user, $pwd);
      $this->conn->exec('SET names utf8');
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }
  }
  public function query($sql = '', $mode = PDO::FETCH_ASSOC) {
    $res = $this->conn->query($sql);
    $res->setFetchMode($mode);
    return $res->fetchAll();
  }

  public function exec($sql = '') {
    return $this->conn->exec($sql) ? ($this->conn->lastInsertId() ? : true) : false;
  }

  /**
   * TODO
   * [execute 预处理]
   * @param  string $sql  [description]
   * @param  array  $data [description]
   * @return [type]       [description]
   * E.g
   * $sql = "INSERT INTO xx(uname,pwd) VALUES (?,?)";
   * execute($sql, ['ldf', '111'])
   *
   * $sql = "INSERT INTO xx(uname,pwd) VALUES (:uname,:pwd)";
   * execute($sql, ['uname' => 'ldf', 'pwd' => '111'])
   */
  public function execute($sql = '', $data = []) {
    $ps = $this->conn->prepare($sql);
    return empty($data) ? $ps->execute() : $ps->execute($data);
  }

  public function begin() {
    return $this->conn->beginTransaction();
  }

  public function commit() {
    return $this->conn->commit();
  }

  public function rollBack() {
    return $this->conn->rollBack();
  }

  public function __destruct() {
    unset($this->conn);
  }

  private function __clone() {}
}
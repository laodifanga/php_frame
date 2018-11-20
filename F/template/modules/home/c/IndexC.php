<?php
class IndexC extends C{
  public function __init() {}

  public function __auto() {}

  public function index() {
    echo '<h1>&emsp;: )</h1><b>LDF： </b><small>'. date('Y-m-d H:i:s'). '</small>';
  }
}
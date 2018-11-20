<?php

/**
 * @Author: LDF
 * @Date:   2018-11-17 16:01:21
 * @Last Modified by:   LDF
 * @Last Modified time: 2018-11-17 16:01:27
 */
interface DbInterface {
  public function connect();
  public function query($sql);
  public function exec($sql);
}
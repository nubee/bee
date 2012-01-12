<?php

class nbMysqlUtils
{
  public static function formatPasswordOption($password) {
    if($password != '')
      return ' -p' . $password;
    
    return '';
  }

}
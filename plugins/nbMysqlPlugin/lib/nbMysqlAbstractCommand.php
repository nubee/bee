<?php

abstract class nbMysqlAbstractCommand extends nbCommand
{
  protected function formatPasswordOption($password) {
    if($password)
      return ' -p' . $password;
    
    return '';
  }

}
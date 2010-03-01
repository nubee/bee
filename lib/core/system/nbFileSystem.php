<?php

class nbFileSystem
{
  public static function getFileName($filename) {
    if(!is_file($filename))
      return '';
    
    return basename($filename);
  }
}
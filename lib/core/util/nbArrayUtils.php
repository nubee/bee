<?php

class nbArrayUtils
{
  public static function getAssociative($array, $path = '')
  {
    $result = array();
    if(! is_array($array))
      return $result;
    foreach($array as $key => $value) {
      if(strlen($path))
        $key = $path.'_'.$key;
      if(self::isAssociative($value))
        $result = array_merge(self::getAssociative($value,$key),$result);
      else
        $result[$key] = $value;
    }
    return $result;
  }

  private static function isAssociative($array)
  {
    return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
  }
 
}
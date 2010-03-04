<?php

class nbConfig
{
  public static $config = array();

  public static function has($path)
  {
    $keys = explode('_', $path);

    $config = self::$config;
    while($k = array_shift($keys)) {
      if(!isset($config[$k]))
        return false;
      $config = $config[$k];
    }
    
    return true;
  }

  public static function set($path, $value)
  {
    self::setElementByPath($path, $value, self::$config);
  }

  public static function get($path, $default = null)
  {
    if(!self::has($path))
      return $default;

    $keys = explode('_', $path);
    $conf = self::$config;
    while($k = array_shift($keys)) {
      if(!isset($conf[$k]))
        throw new InvalidArgumentException('[nbConfiguration::getElementByPath] Invalid key: ' . $path);
      $conf = $conf[$k];
    }
    return $conf;
  }

  public static function reset()
  {
    self::$config = array();
  }

  public static function getAll($associative = false)
  {
    return $associative ? self::getAssociative(self::$config) : self::$config;
  }

  public static function remove($key)
  {
    unset(self::$config[$key]);
  }

  private static function setElementByPath($path, $value, &$array)
  {
    $keys = explode('_', $path);
    $k = array_shift($keys);
    if(0 === count($keys))
      $array[$k] = $value;
    else {
      $path = implode('_', $keys);
      if(!isset($array[$k]) || !is_array($array[$k]))
        $array[$k] = array();
      self::setElementByPath($path, $value, $array[$k]);
    }
  }
  
  public static function add($array = array())
  {
    foreach(self::getAssociative($array) as $path=>$value)
      self::set($path,$value);
  }

  public static function getAssociative($array, $path = '')
  {
    $result = array();
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

  private function isAssociative($array)
  {
    return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
  }
}

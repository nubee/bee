<?php
class nbConfiguration {

  public static $configuration = array();

  public static function has($keyPath)
  {
    try {
     self::getElementByPath($keyPath);
     return true;
    }
    catch (InvalidArgumentException $e) {
      return false;
    }
  }

  public static function set($keyPath, $value)
  {
    self::setElementByPath($keyPath, $value);
  }

  public static function get($keyPath, $default = null)
  {
    try {
     return self::getElementByPath($keyPath);
    }
    catch (InvalidArgumentException $e) {
      return $default;
    }
  }

  public static function reset()
  {
    self::$configuration = array();
  }

  public static function getAll()
  {
    return self::$configuration;
  }

  public static function remove($key)
  {
    unset(self::$configuration[$key]);
  }

  private static function getElementByPath($path)
  {
    $keys = explode('_', $path);
    $conf = self::$configuration;
    while($k = array_shift($keys)) {
      if(!isset($conf[$k]))
        throw new InvalidArgumentException('[nbConfiguration::getElementByPath] Invalid key');
      $conf = $conf[$k];
    }
    return $conf;
  }

  private static function setElementByPath($path, $value, &$ary = null)
  {
    $keys = explode('_', $path);
    if(is_null($ary))
      $ary =& self::$configuration;
    $k = array_shift($keys);
    if(0 === count($keys))
      $ary[$k] = $value;
    else {
      $path = implode('_', $keys);
      if(!is_array($ary[$k]))
        $ary[$k] = array();
      self::setElementByPath($path, $value, $ary[$k]);
    }
  }
  
  public static function add($ary = array())
  {
    foreach(self::getAssociative($ary) as $path=>$value)
      self::set($path,$value);
  }

  public static function getAssociative($ary, $path='')
  {
    $result = array();
    foreach($ary as $key => $value)
    {
//      if(! (is_array($value) &&  self::is_assoc($value)))
//        $result[$key] = $value;
        if(strlen($path))
          $key = $path.'_'.$key;
        if(self::is_assoc($value))
        {
          $result = array_merge(self::getAssociative($value,$key),$result);
        }
        else
        {
          $result[$key] = $value;
        }
    }
    return $result;
  }

  //TODO: must be moved in a common file (ArrayUtils ???)
  private function is_assoc($array)
  {
      return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
  }

}

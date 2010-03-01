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

  public static function add($keyPath, $value)
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
}

<?php
class nbConfiguration {

  public static $configuration = array();

  public static function has($key)
  {
    return array_key_exists($key, self::$configuration);
  }

  public static function add($key, $value)
  {
    self::$configuration[$key] = $value;
  }

  public static function get($key, $default = null)
  {
    if(!self::has($key))
      return $default;
    return self::$configuration[$key];
  }
}
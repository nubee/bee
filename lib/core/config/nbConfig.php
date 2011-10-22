<?php

class nbConfig
{
  private static $configuration = null;
  
  public static function init() {
    self::$configuration = new nbConfiguration();
  }
  
  public static function getConfiguration() {
    return self::$configuration;
  }

  public static function has($path)
  {
    return self::$configuration->has($path);
  }

  public static function set($path, $value)
  {
    self::$configuration->set($path, $value);
  }

  public static function get($path, $default = null)
  {
    return self::$configuration->get($path, $default);
  }

  public static function reset()
  {
    self::init();
  }

  public static function getAll($associative = false)
  {
    return self::$configuration->getAll($associative);
  }

  public static function remove($key)
  {
    self::$configuration->remove($key);
  }

  private static function setElementByPath($path, $value, &$array)
  {
    self::$configuration->setElementByPath($path, $value, $array);
  }

  public static function add($array = array(), $prefix = '', $replaceTokens = false)
  {
    self::$configuration->add($array, $prefix, $replaceTokens);
  }

}

nbConfig::init();

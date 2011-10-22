<?php

class nbConfig
{
  public static $config = array();

  public static function has($path)
  {
    $keys = explode('_', $path);

    $config = self::$config;
    while($k = array_shift($keys)) {
      if(!is_array($config))
        return false;
      if(!key_exists($k, $config))
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
      if(!key_exists($k, $conf))
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
    return $associative ? nbArrayUtils::getAssociative(self::$config) : self::$config;
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

  public static function add($array = array(), $prefix = '', $replaceTokens = false)
  {
    $prefix = trim($prefix, ' _.,?');
    if(strlen($prefix) > 0)
      $prefix .= '_';
    
    foreach(nbArrayUtils::getAssociative($array) as $path => $value) {
      self::set($prefix . $path, $value);
    }
    
    while($replaceTokens) {
      $replaceTokens = false;
      
      foreach(nbArrayUtils::getAssociative(self::$config) as $path => $value) {
        $tokenizer = new ConfigTokenReplacer($prefix);
        $replaced = preg_replace_callback('/%(.*)%/', array(&$tokenizer, 'replaceTokens'), $value);

        if($replaced != $value) {
          self::set($path, $replaced);
          $replaceTokens = true;
        }
      }
    }
    
  }

}

class ConfigTokenReplacer
{
  private $prefix;
  
  public function __construct($prefix) {
    $this->prefix = $prefix;
  }
  
  public function replaceTokens($match) {
    $value = $this->prefix . $match[1];
    if(nbConfig::has($value)) {
      echo 'found: ' . $value . "\n";
      return nbConfig::get($value);
    }
    
    return $match[0];
  }
}

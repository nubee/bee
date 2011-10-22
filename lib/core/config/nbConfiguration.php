<?php

class nbConfiguration
{
  private $config = array();

  public function has($path)
  {
    $keys = explode('_', $path);

    $config = $this->config;
    while($k = array_shift($keys)) {
      if(!is_array($config))
        return false;
      
      if(!key_exists($k, $config))
        return false;
      
      $config = $config[$k];
    }

    return true;
  }

  public function set($path, $value)
  {
    $this->setElementByPath($path, $value, $this->config);
  }

  public function get($path, $default = null)
  {
    if(!$this->has($path))
      return $default;

    $keys = explode('_', $path);
    $conf = $this->config;
    while($k = array_shift($keys)) {
      if(!key_exists($k, $conf))
        throw new InvalidArgumentException('[nbConfiguration::getElementByPath] Invalid key: ' . $path);
      $conf = $conf[$k];
    }
    return $conf;
  }

  public function reset()
  {
    $this->config = array();
  }

  public function getAll($associative = false)
  {
    return $associative ? nbArrayUtils::getAssociative($this->config) : $this->config;
  }

  public function remove($key)
  {
    unset($this->config[$key]);
  }

  private function setElementByPath($path, $value, &$array)
  {
    $keys = explode('_', $path);
    $k = array_shift($keys);
    if(0 === count($keys))
      $array[$k] = $value;
    else {
      $path = implode('_', $keys);
      if(!isset($array[$k]) || !is_array($array[$k]))
        $array[$k] = array();
      $this->setElementByPath($path, $value, $array[$k]);
    }
  }

  public function add($array = array(), $prefix = '', $replaceTokens = false)
  {
    $prefix = trim($prefix, ' _.,?');
    if(strlen($prefix) > 0)
      $prefix .= '_';
    
    foreach(nbArrayUtils::getAssociative($array) as $path => $value) {
      $this->set($prefix . $path, $value);
    }
    
    while($replaceTokens) {
      $replaceTokens = false;
      
      foreach(nbArrayUtils::getAssociative($this->config) as $path => $value) {
        $tokenizer = new ConfigTokenReplacer($this, $prefix);
        $replaced = preg_replace_callback('/%([^%]*)%/', array(&$tokenizer, 'replaceTokens'), $value);

        if($replaced != $value) {
          $this->set($path, $replaced);
          $replaceTokens = true;
        }
      }
    }
    
  }

}

class ConfigTokenReplacer
{
  private $configuration;
  private $prefix;
  
  public function __construct(nbConfiguration $configuration, $prefix) {
    $this->configuration = $configuration;
    $this->prefix = $prefix;
  }
  
  public function replaceTokens($match) {
    $value = $this->prefix . $match[1];
    echo 'checking: ' . $value . "\n";
    if($this->configuration->has($value)) {
      return $this->configuration->get($value);
    }
    
    return $match[0];
  }
}

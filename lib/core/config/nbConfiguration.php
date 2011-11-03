<?php

class nbConfiguration
{
  private $config = array();

  const REQUIRED = 'required';
  const DIR_EXISTS = 'dir-exists';
  const FILE_EXISTS = 'file-exists';
  const DEFAULT_VALUE = 'default';

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
      $replaceTokens = $this->replaceTokens($this->config, $prefix);
    }
  }

  public function replaceTokens($config, $prefix, $parentPath = '')
  {
    $replaceTokens = false;

    foreach(nbArrayUtils::getAssociative($config) as $path => $value) {
      if(is_array($value)) {
        if(!nbArrayUtils::isAssociative($value)) 
          $replaceTokens = $this->replaceTokens($value, $prefix, $path . '_');
        else  {
          foreach($value as $i => $v) {
            //echo $v. "\n";
            $replaceTokens = $this->replaceTokens($v, $prefix, $path . '_' . $i . '_');
          }
        }
      }
      else {
        $tokenizer = new ConfigTokenReplacer($this, $prefix);
        $replaced = preg_replace_callback('/%([^%]+)%/', array(&$tokenizer, 'replaceTokens'), $value);

        if($replaced != $value) {
          $this->set($parentPath . $path, $replaced);
            
          $replaceTokens = true;
        }
      }
    }

    return $replaceTokens;
  }

}

class ConfigTokenReplacer
{
  private $configuration;
  private $prefix;

  public function __construct(nbConfiguration $configuration, $prefix)
  {
    $this->configuration = $configuration;
    $this->prefix = $prefix;
  }

  public function replaceTokens($match)
  {
    $value = $this->prefix . $match[1];
    //echo sprintf("Checking: %s (%s)\n", $value, $match[0]);
    
    if($this->configuration->has($value)) {
      if(is_array($this->configuration->get($value))) {
        throw new InvalidArgumentException('Cannot replace a value with an array (checking: ' . $value . ')');
      }

      //echo sprintf("Found: %s\n", $this->configuration->get($value));
      return $this->configuration->get($value);
    }

    return $match[0];
  }

}

<?php

class nbConfigurationChecker {
  private $logger = null;
  private $verbose = false;
  private $errors = array();
  
  public function __construct(array $options = array()) {
    $logger = isset($options['logger']) ? $options['logger'] : null;
    if($logger && !$logger instanceof nbLogger)
      throw new InvalidArgumentException('Undefined logger class');
    
    $this->logger = $logger;
    $this->verbose = isset($options['verbose']) ? $options['verbose'] : false;
  }
  
  public function checkConfigFile($templateFile, $configFile) {
    if (!file_exists($configFile))
      throw new Exception(sprintf('Filename %s does not exist', $configFile));
    
    $config = sfYaml::load($configFile);
    
    return $this->check($templateFile, $config);
  }
  
  public function checkWholeConfig($templateFile) {
    return $this->check($templateFile, nbConfig::getAll(true));
  }
  
  public function check($templateFile, $config) {
    if (!file_exists($templateFile))
      throw new Exception(sprintf('Template %s does not exist', $templateFile));
    
    $template = sfYaml::load($templateFile);
    
    $this->errors = array();
    $this->doCheck('', $config, $template);
    
    if($this->hasErrors()) {
      $message = "Configuration has errors: \n";
      foreach($this->errors as $key => $error) {
        $message .= sprintf("%s: %s\n", $key, $error); 
      }
      
      throw new Exception($message);
    }
    
    return true;
  }
  
  private function doCheck($path, $first, $second) {
    $required = false;
    
    foreach($second as $key => $value) {
      $this->logLine('Checking ' . $key);
      
      $childRequired = false;
      
      if(is_array($value)) {
        $subpath = $path . $key. '_';
        $firstKey = isset($first[$key]) ? $first[$key] : null;
        $childRequired = $this->doCheck($subpath, $firstKey, $value);
        
        if($childRequired && !$firstKey) {
          $this->errors[$path . $key] = 'required';
          $this->logLine(sprintf('Required field "%s" not found', $key), nbLogger::ERROR);
        }
      }
      
      if($key == 'required') {
        //$this->logLine('Field is required', nbLogger::INFO);
        // Whatever value of required will set "required" to true
        $required = $value || $childRequired;
      }
    }
    
    return $required;
  }
  
  public function logLine($text, $level = null)
  {
    if($this->verbose && $this->logger)
      $this->logger->logLine($text, $level);
  }
  
  public function hasErrors()
  {
    return count($this->errors) > 0;
  }
  
  public function getErrors()
  {
    return $this->errors;
  }
}

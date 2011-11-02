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
  
  public function check($templateFile, $values) {
    if (!file_exists($templateFile))
      throw new Exception(sprintf('Template %s does not exist', $templateFile));
    
    $template = new nbConfiguration();
    $template->add(sfYaml::load($templateFile), '');
    
    $config = new nbConfiguration();
    $config->add($values, '', true);
    
    $this->errors = array();

    $this->doCheck('', $config->getAll(), $template->getAll());
    
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
    $options = array();
    
    foreach($second as $key => $value) {
      //$this->logLine('Checking: ' . $key);
      
      $childRequired = false;
      
      if(is_array($value)) {
        $subkey = $path . $key;
        
        $firstKey = isset($first[$key]) ? $first[$key] : null;

        $options = $this->doCheck($subkey . '_', $firstKey, $value);
        
        $required = isset($options[nbConfiguration::REQUIRED]) && $options[nbConfiguration::REQUIRED];
        
        if($required) {
          if(!$firstKey) {
//          $this->logLine(sprintf('Required field "%s" not found', $subkey), nbLogger::ERROR);
            $this->errors[$path . $key] = nbConfiguration::REQUIRED;
          }
          unset($options[nbConfiguration::REQUIRED]);
        }
        
        if(isset($options[nbConfiguration::DIR_EXISTS])) {
//          $this->logLine(sprintf('Check if directory "%s" exists', $firstKey), nbLogger::INFO);
          // Check if the key exists, when the key is not required
          // Otherwise, the required test will fail
          if(isset($first[$key]) && !is_dir($firstKey)) {
            $this->errors[$path . $key] = sprintf('Directory "%s" does not exist', $firstKey);
          }
          unset($options[nbConfiguration::DIR_EXISTS]);
        }
        
        if(isset($options[nbConfiguration::FILE_EXISTS]) ) {
//          $this->logLine(sprintf('Check if file "%s" exists', $firstKey), nbLogger::INFO);
          // Check if the key exists, when the key is not required
          // Otherwise, the required test will fail
          if(isset($first[$key]) && (is_array($firstKey) || !file_exists($firstKey))) {
            $this->errors[$path . $key] = sprintf('File "%s" does not exist', $firstKey);
          }
          unset($options[nbConfiguration::FILE_EXISTS]);
        }
      }
      
      if($key == nbConfiguration::REQUIRED) {
        //$this->logLine(sprintf('Field "%s" is required', $key), nbLogger::INFO);
        // Whatever value of required will set "required" to true
        $options[nbConfiguration::REQUIRED] = $value;// || $childRequired;
      }
      
      if($key == nbConfiguration::DIR_EXISTS) {
        $options[nbConfiguration::DIR_EXISTS] = $value;
      }

      if($key == nbConfiguration::FILE_EXISTS) {
        $options[nbConfiguration::FILE_EXISTS] = $value;
      }
      
    }
    
    return $options;
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

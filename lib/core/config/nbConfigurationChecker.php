<?php

class nbConfigurationChecker {
  private $logger = null;
  private $verbose = false;
  
  public function check($template, $config, array $options = array()) {
    if (!file_exists($template))
      throw new Exception(sprintf('Template %s does not exist', $template));

    if (!file_exists($config))
      throw new Exception(sprintf('Filename %s does not exist', $config));
    
    $logger = isset($options['logger']) ? $options['logger'] : null;
    if($logger && !$logger instanceof nbLogger)
      throw new InvalidArgumentException('Undefined logger class');
    
    $this->logger = $logger;
    $this->verbose = isset($options['verbose']) ? $options['verbose'] : false;
    
    $configParser = sfYaml::load($config);
    $templateParser = sfYaml::load($template);
    
    $this->doCheck($configParser, $templateParser);
    
    return true;
  }
  
  private function doCheck($first, $second) {
    $required = false;
    
    foreach($second as $key => $value) {
      $this->logLine('Checking ' . $key);
      
      $childRequired = false;
      
      if(is_array($value)) {
        $firstKey = isset($first[$key]) ? $first[$key] : null;
        $childRequired = $this->doCheck($firstKey, $value);
        
        if($childRequired && !$firstKey) {
          $this->logLine('Required field not found', nbLogger::ERROR);
          throw new Exception('Undefined key: ' . $key);
        }
      }
      
      if($key == 'required') {
        $this->logLine('Field is required', nbLogger::INFO);
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
}

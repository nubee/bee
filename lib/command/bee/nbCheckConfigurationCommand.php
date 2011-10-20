<?php

class nbCheckConfigurationCommand extends nbCommand {

  protected function configure() {
    $this->setName('bee:check-configuration')
      ->setBriefDescription('Check configuration for a yml file')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('template', nbArgument::REQUIRED, 'Config template'),
        new nbArgument('filename', nbArgument::REQUIRED, 'Config file name'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $template = $arguments['template'];
    $config = $arguments['filename'];
    
    if (!file_exists($template))
      throw new Exception(sprintf('Template %s does not exist', $template));

    if (!file_exists($config))
      throw new Exception(sprintf('Filename %s does not exist', $config));
      
    $configParser = sfYaml::load($config);
    $templateParser = sfYaml::load($template);
    
    $this->check($configParser, $templateParser);

    return true;
  }
  
  private function check($first, $second) {
    $required = false;
    
    foreach($second as $key => $value) {
      $this->logLine('Checking ' . $key);
      
      $childRequired = false;
      
      if(is_array($value)) {
        $firstKey = isset($first[$key]) ? $first[$key] : null;
        $childRequired = $this->check($firstKey, $value);
        
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

}
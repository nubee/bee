<?php

class nbGenerateConfigurationCommand extends nbCommand {

  protected function configure() {
    $this->setName('bee:generate-configuration')
      ->setBriefDescription('Generate configuration from a template file')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('template', nbArgument::REQUIRED, 'Config template'),
        new nbArgument('destination', nbArgument::REQUIRED, 'Config file destination name'),
      )));

    $this->addOption(
      new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'Overwrite the existing configuration')
    );
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $template = $arguments['template'];
    $destination = $arguments['destination'];

//    if (!file_exists($config))
//      throw new Exception(sprintf('Filename %s does not exist', $config));
//      
      
    $generator = new nbConfigurationGenerator();
    
    $generator->generate($template, $destination);

    return true;
  }
  
  private function generate($array) {
    $config = '';
    
    foreach($array as $key => $value) {
      if(is_array($value)) {
        if(!$config)
          $config = array();
        
        $child = $this->generate($value);

        $config[$key] = $child;
      }
      else {
        if($key == 'required')
          continue;

        if($key == 'default')
          return $value;
        
        $config[$key] = '';
      }
    }
    
    return $config;
  }

}
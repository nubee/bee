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
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $template = $arguments['template'];
    $destination = $arguments['destination'];
    
    if (!file_exists($template))
      throw new Exception(sprintf('Template %s does not exist', $template));

//    if (!file_exists($config))
//      throw new Exception(sprintf('Filename %s does not exist', $config));
//      
      
    $templateParser = sfYaml::load($template);
    
    $yml = $this->generate($templateParser);
    $yml = sfYaml::dump($yml);
    $yml = str_replace('\'\'', '', $yml);
    file_put_contents($destination, $yml);

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
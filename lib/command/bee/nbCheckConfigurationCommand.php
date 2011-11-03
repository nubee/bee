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
      
    $checker = new nbConfigurationChecker(array(
      'logger' => $this->getLogger(), 
      'verbose' => $this->isVerbose()
    ));
    
    $checker->checkConfigFile($template, $config);

    return true;
  }

}
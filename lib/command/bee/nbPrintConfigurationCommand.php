<?php

class nbPrintConfigurationCommand extends nbCommand {

  protected function configure() {
    $this->setName('bee:print-configuration')
      ->setBriefDescription('Print configuration from a yml file')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->addOption(
        new nbOption('filename', 'f', nbOption::PARAMETER_REQUIRED, 'Config file name')
      );
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $filename = isset($options['filename']) ? $options['filename'] : null;

    $printer = new nbConfigurationPrinter();
    $printer->addConfiguration(nbConfig::getAll());
    
    if($filename) {
      $this->logLine(sprintf('bee configuration (file: %s)', $filename), nbLogger::COMMENT);

      $printer->addConfigurationFile($filename);
    }
    else {
      $dirs = array('./', nbConfig::get('nb_config_dir'));
      $this->logLine(sprintf('bee configuration (dirs: %s)', implode(', ', $dirs)), nbLogger::COMMENT);
      
      $finder = nbFileFinder::create('file')->add('*.yml')->maxdepth(0);
        
      foreach($dirs as $dir) {
        $files = $finder->in($dir);
        
        foreach($files as $file) {
          $this->logLine('Adding ' . $file, nbLogger::COMMENT);
          $printer->addConfigurationFile($file);
        }
      }
    }
    
    $this->logLine($printer->printAll());
    
    return true;
  }

}

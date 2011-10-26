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

class nbConfigurationPrinter {
  private $configuration = null;
  
  public function __construct() {
    $this->configuration = new nbConfiguration();
  }
  
  public function addConfiguration(array $values) {
    $this->configuration->add($values);
  }

  public function addConfigurationFile($filename) {
    $yamlParser = new nbYamlConfigParser($this->configuration);
    $yamlParser->parseFile($filename, '', true);
  }
  
  public function printAll() {
    $text = '';
    foreach($this->configuration->getAll(true) as $key => $value)
//      $this->logLine($this->formatPrint($key, $value, 0));
      $text .= $this->formatPrint($key, $value, 0);
    
    return $text;
  }
  
  
  private function formatPrint($key, $value, $indent) {
    $text = '';
    if(is_array($value)) {
      foreach($value as $k => $v) {
        $text .= $this->formatPrint($k, $v, $indent + 1);
      }
    }
    else 
      $text = $value;
    
    $text = preg_replace_callback('/%([^%]*)%/', array(&$this, 'highlight'), $text);
    
    return sprintf("\n%s%s: %s", str_repeat('  ', $indent), $key, $text);
  }
  
  private function highlight($match) {
    return '<info>' . $match[0] . '</info>';
  }
  
}
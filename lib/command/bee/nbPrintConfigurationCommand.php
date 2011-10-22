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

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('filename', nbArgument::REQUIRED, 'Config file name'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $filename = $arguments['filename'];

    $configuration = new nbConfiguration();
    
    $yamlParser = new nbYamlConfigParser($configuration);
    $yamlParser->parseFile($filename, '', true);
    
    foreach($configuration->getAll(true) as $key => $value)
//      $this->logLine($this->formatPrint($key, $value, 0));
      $this->logLine($this->formatPrint($key, $value, 0));
    
    return true;
  }
  
  private function formatPrint($key, $value, $indent) {
    $text = $key;
    if(is_array($value)) {
      foreach($value as $k => $v) {
        $text .= $this->formatPrint($k, $v, $indent + 1);
      }
    }
    else 
      $text = $value;
    
    $text = preg_replace_callback('/%([^%]*)%/', array(&$this, 'highlight'), $text);
    
    return sprintf("%s%s: %s", str_repeat('--', $indent), $key, $text);
  }
  
  private function highlight($match) {
    return '<info>' . $match[0] . '</info>';
  }

}
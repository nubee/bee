<?php

class nbConfigurationPrinter {

  private $configuration = null;

  public function __construct() {
    $this->configuration = new nbConfiguration();
  }

  public function addConfiguration(array $values) {
    $this->configuration->add($values);
  }
  
  public function addConfigurationErrors(array $errors) {
    $list = array();
    foreach($errors as $key => $error) {
      $list[$key] = '<error>' . $error . '</error>';
    }
    
    $this->addConfiguration($list);
  }

  public function addConfigurationFile($filename) {
    $yamlParser = new nbYamlConfigParser($this->configuration);
    $yamlParser->parseFile($filename, '', true);
  }

  public function printAll() {
    $text = '';
    foreach ($this->configuration->getAll(true) as $key => $value)
//      $this->logLine($this->formatPrint($key, $value, 0));
      $text .= $this->formatPrint($key, $value, 0);

    return $text;
  }

  private function formatPrint($key, $value, $indent) {
    $text = '';
    if (is_array($value)) {
      foreach ($value as $k => $v) {
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
<?php
class nbYamlConfigurationParser
{
  private $yamlParser = null, $result = array();
  public function __construct()
  {
    $this->yamlParser = new sfYamlParser();
  }

  public function get()
  {
    return $this->result;
  }

  public function parse($yamlString)
  {
    $this->result = array_merge($this->result, $this->yamlParser->parse($yamlString));
  }

  public function parseFile($filePath)
  {
    $this->parse(file_get_contents($filePath));
  }

  public function clear()
  {
    $this->result = array();
  }

}

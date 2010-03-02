<?php
class nbYamlConfigurationParser
{
  private $yamlParser = null;

  public function __construct()
  {
    $this->yamlParser = new sfYamlParser();
  }

  public function parse($yamlString)
  {
    nbConfiguration::add($this->yamlParser->parse($yamlString));
  }

  public function parseFile($filePath)
  {
    $this->parse(file_get_contents($filePath));
  }
}

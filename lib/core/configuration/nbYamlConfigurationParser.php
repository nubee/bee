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
    if(! file_exists($filePath))
      throw new InvalidArgumentException(sprintf('[nbYamlConfigurationParser::parseFile] file \'%s\' does not exist',$filePath ));
    $this->parse(file_get_contents($filePath));
  }
}

<?php
class nbYamlConfigParser
{
  private $yamlParser = null;

  public function __construct()
  {
    $this->yamlParser = new sfYamlParser();
  }

  public function parse($yamlString, $prefix = '')
  {
    nbConfig::add($this->yamlParser->parse($yamlString), $prefix);
  }

  public function parseFile($filePath, $prefix = '')
  {
    if(! file_exists($filePath))
      throw new InvalidArgumentException(sprintf('[nbYamlConfigurationParser::parseFile] file \'%s\' does not exist',$filePath ));
    
    $this->parse(file_get_contents($filePath), $prefix);
  }
}

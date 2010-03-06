<?php
class nbYamlConfigParser
{
  public function parse($yamlString, $prefix = '')
  {
    nbConfig::add(sfYaml::load($yamlString), $prefix);
  }

  public function parseFile($filePath, $prefix = '')
  {
    if(! file_exists($filePath))
      throw new InvalidArgumentException(sprintf('[nbYamlConfigurationParser::parseFile] file \'%s\' does not exist',$filePath ));
    $this->parse($filePath, $prefix);
  }
}

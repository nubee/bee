<?php

class nbYamlConfigParser
{
  public function parse($yamlString, $prefix = '', $replaceTokens = false)
  {
    nbConfig::add(sfYaml::load($yamlString), $prefix, $replaceTokens);
  }

  public function parseFile($file, $prefix = '', $replaceTokens = false)
  {
    if(!file_exists($file))
      throw new InvalidArgumentException(sprintf('File %s does not exist', $file));
    
    $this->parse($file, $prefix, $replaceTokens);
  }

}

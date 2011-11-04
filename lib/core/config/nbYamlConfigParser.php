<?php

class nbYamlConfigParser
{
  private $configuration;

  public function __construct(nbConfiguration $configuration = null)
  {
    $this->configuration = $configuration;
  }

  public function parse($yaml, $prefix = '', $replaceTokens = false)
  {
    $values = sfYaml::load($yaml);

    if($this->configuration)
      $this->configuration->add($values, $prefix, $replaceTokens);
    else
      nbConfig::add($values, $prefix, $replaceTokens);
  }

  public function parseFile($file, $prefix = '', $replaceTokens = false)
  {
    if(!file_exists($file))
      throw new InvalidArgumentException(sprintf('File %s does not exist', $file));

    $this->parse($file, $prefix, $replaceTokens);
  }

}

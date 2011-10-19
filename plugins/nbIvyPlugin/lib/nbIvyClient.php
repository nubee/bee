<?php

class nbIvyClient
{
  private
    $settings = null,
    $ivyFile = '';

  public function  __construct($ivyFile = 'ivy.xml')
  {
    $this->settings = nbConfig::get('nb_ivy_settings');
    $this->ivyFile = $ivyFile;
  }

  public function getRetrieveCmdLine()
  {
    $props = parse_ini_file(nbConfig::get('nb_ivy_properties'));

    $command = 'java -jar "' . nbConfig::get('nb_ivy_executable') . '" ';
    $command .= '-settings "' . $this->settings . '" ';
    $command .= '-retrieve ' . $props['ivy.retrieve.pattern'] . ' -ivy "' . $this->ivyFile . '"';

    return $command;
  }

  public function getPublishCmdLine($local = false)
  {
    $props = parse_ini_file(nbConfig::get('nb_ivy_properties'));

    $command = 'java -jar "' . nbConfig::get('nb_ivy_executable') . '" ';
    $command .= '-settings "' . $this->settings . '" ';
    $command .= '-publish ';
    if($local)
      $command .= 'local';
    else
      $command .= 'shared';
    
    return $command;
  }
}

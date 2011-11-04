<?php

class DummyCommandWithConfiguration extends nbCommand
{
  protected function configure()
  {
    $this->setName('dummyconfig');
  }
  
  protected function execute(array $arguments = array(), array $options = array())
  {
    $configDir = dirname(__FILE__) . '/../../data/config';
    $configFilename = $configDir . '/dummyconfig.yml';
    
    $this->loadConfiguration($configDir, $configFilename);
    
    //print_r(nbConfig::getAll(true));
  }
  
  public function getConfig($key) {
    return nbConfig::get($key);
  }

}
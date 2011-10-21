<?php

class nbTestCommandLineParser extends nbCommandLineParser
{

  public function getDefaultConfigurationDirs()
  {
    return array(
      nbConfig::get('nb_data_dir') . '/config',
      dirname(__FILE__) . '/../config/'
    );
  }

}
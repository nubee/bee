<?php

class nbConfigurationGenerator
{

  public function generate($template, $destination, $force = false)
  {
    if(!file_exists($template))
      throw new Exception(sprintf('Template %s does not exist', $template));
    
    if(file_exists($destination) && !$force)
      throw new Exception('Cannot overwrite ' . $destination);

    $templateParser = sfYaml::load($template);

    $yml = $this->doGenerate($templateParser);
    
    $yml = sfYaml::dump($yml, 10);
    $yml = str_replace('\'\'', '', $yml);
    file_put_contents($destination, $yml);
    
    return true;
  }

  private function doGenerate($array)
  {
    $config = '';

    foreach($array as $key => $value) {
      if(is_array($value)) {
        if(!$config)
          $config = array();

        $child = $this->doGenerate($value);

        $config[$key] = $child;
      }
      else {
        if($key == nbConfiguration::REQUIRED)
          continue;
        if($key == nbConfiguration::DIR_EXISTS)
          continue;
        if($key == nbConfiguration::FILE_EXISTS)
          continue;

        if($key == nbConfiguration::DEFAULT_VALUE)
          return $value;

        $config[$key] = '';
      }
    }

    return $config;
  }

}
<?php

class nbAntClient {

  private $options;

  function setLibraryPath($libPath) {
    $this->options .= " -lib $libPath";
  }

  function setPropertyFile($propertyFilePath) {
    $this->options .= " -propertyfile $propertyFilePath";
  }

  function getCommandLine($command, $arguments = array(), $options = array()) {
    $cmdLine = "ant";
    if ($this->options)
      $cmdLine .= "$this->options";
    $cmdLine .= " $command";

    if ($arguments)
    {
      foreach ($arguments as $argName => $argValue)
      {
        $cmdLine .= " -D$argName=$argValue";
      }
    }

    if ($options)
    {
      foreach ($options as $optionName => $optionValue)
      {
        if ($optionValue == '')
          $optionValue = 'true';
        $cmdLine .= " -D$optionName=$optionValue";
      }
    }
    return $cmdLine;
  }
}

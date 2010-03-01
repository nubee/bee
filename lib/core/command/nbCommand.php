<?php

/**
 * Represents a command to execute.
 *
 * @package    bee
 * @subpackage argument
 */
abstract class nbCommand
{
  private
    $name,
    $namespace,
    $arguments = null,
    $options = null;

  public function __construct()
  {
    $this->arguments = new nbArgumentSet();
    $this->options = new nbOptionSet();
  }
  
  public function setName($name)
  {
    $pos = strpos($name, ':');
    if (false !== $pos) {
      $namespace = substr($name, 0, $pos);
      $name = substr($name, $pos + 1);
    }
    else
      $namespace = '';

    if($name === null || strlen($name) == 0)
      throw new InvalidArgumentException('[nbCommand::setName] Name can\'t be empty');

    $this->namespace = $namespace;
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getNamespace()
  {
    return $this->namespace;
  }

  public function getFullName()
  {
    $namespace = ($this->namespace != null ? $this->namespace . ':' : '');
    return $namespace . $this->name;
  }

  public function setArguments(nbArgumentSet $arguments)
  {
    $this->arguments = $arguments;
  }

  public function getArguments()
  {
    return $this->arguments;
  }

  public function setOptions(nbOptionSet $options)
  {
    $this->options = $options;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function hasShortcut($shortcut)
  {
    $pos = strpos($shortcut, ':');
    if (false !== $pos) {
      $namespace = substr($shortcut, 0, $pos);
      $name = substr($shortcut, $pos + 1);
    }
    else {
      $namespace = '';
      $name = $shortcut;
    }

    if(substr($this->namespace, 0, strlen($namespace)) != $namespace)
      return false;
    if(substr($this->name, 0, strlen($name)) != $name)
      return false;

    return true;
  }

  public function getSynopsys()
  {
    $synopsys = 'bee ' . $this->getFullname();
    $synopsys .= (string)$this->arguments;
    $synopsys .= (string)$this->options;
    return $synopsys;
  }
}

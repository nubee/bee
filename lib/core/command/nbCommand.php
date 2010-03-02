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
    $briefDescription = '',
    $description = '',
    $arguments = null,
    $options = null;

  public function __construct()
  {
    $this->arguments = new nbArgumentSet();
    $this->options = new nbOptionSet();

    $this->configure();
  }

  public function run(nbCommandLineParser $parser, $commandLine)
  {
    $parser->addArguments($this->getArguments());
    $parser->addOptions($this->getOptions());

    $parser->parse($commandLine);
    
    $this->execute($parser->getArgumentValues(), $parser->getOptionValues());
  }

  protected abstract function configure();
  protected abstract function execute(array $arguments = array(), array $options = array());
  
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

    return $this;
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
    return $this;
  }

  public function getArguments()
  {
    return $this->arguments;
  }

  public function setOptions(nbOptionSet $options)
  {
    $this->options = $options;
    return $this;
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

  public function setBriefDescription($description)
  {
    $this->briefDescription = $description;
    return $this;
  }

  public function getBriefDescription()
  {
    return $this->briefDescription;
  }

  public function setDescription($description)
  {
    $this->description = $description;
    return $this;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function getSynopsys()
  {
    $synopsys = 'bee ' . $this->getFullname();
    $synopsys .= (string)$this->arguments;
    $synopsys .= (string)$this->options;
    return $synopsys;
  }
}

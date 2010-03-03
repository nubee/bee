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

  private $logger;

  public function __construct()
  {
    $this->argumentSet = new nbArgumentSet();
    $this->optionSet = new nbOptionSet();

    $this->logger = nbLogger::getInstance();
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
    $this->argumentSet = $arguments;
    return $this;
  }

  public function getArguments()
  {
    return $this->argumentSet;
  }

  public function getArgumentsArray()
  {
    return $this->argumentSet->getArguments();
  }

  public function setOptions(nbOptionSet $options)
  {
    $this->optionSet = $options;
    return $this;
  }

  public function getOptions()
  {
    return $this->optionSet;
  }

  public function getOptionsArray()
  {
    return $this->optionSet->getOptions();
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
    $synopsys .= (string)$this->argumentSet;
    $synopsys .= (string)$this->optionSet;
    return $synopsys;
  }

  public function log($text, $level = null)
  {
    $this->logger->log($text, $level);
  }

  public function format($text, $level)
  {
    return $this->logger->format($text, $level);
  }

  public function formatLine($text, $level)
  {
    return $this->logger->formatLine($text, $level);
  }
}

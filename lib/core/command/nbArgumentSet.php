<?php

/**
 * Represent a set of command line arguments.
 *
 * @package    bee
 * @subpackage command
 */
class nbArgumentSet
{
  protected
    $arguments = array(),
    $requiredCount = 0,
    $hasAnArrayArgument = false,
    $hasAnOptionalArgument = false;

  /**
   * Constructor.
   *
   * @param array $arguments An array of nbArgument objects
   */
  public function __construct(array $arguments = array())
  {
    $this->arguments = array();
    $this->requiredCount = 0;
    $this->hasAnOptionalArgument = false;
    $this->hasAnArrayArgument = false;
    $this->addArguments($arguments);
  }

  /**
   * Add an array of nbArgument objects.
   *
   * @param array $arguments An array of nbArgument objects
   */
  public function addArguments(array $arguments = array())
  {
    if (null !== $arguments) {
      foreach ($arguments as $argument)
        $this->addArgument($argument);
    }
  }

  /**
   * Add a nbArgument objects.
   *
   * @param nbArgument $argument A nbArgument object
   */
  public function addArgument(nbArgument $argument)
  {
    if (isset($this->arguments[$argument->getName()]))
      throw new InvalidArgumentException(sprintf('[nbArgumentSet::addArgument] An argument with name "%s" already exist.', $argument->getName()));

    if ($this->hasAnArrayArgument)
      throw new InvalidArgumentException('[nbArgumentSet::addArgument] Cannot add an argument after an array argument.');

    if ($argument->isRequired() && $this->hasAnOptionalArgument)
      throw new InvalidArgumentException('[nbArgumentSet::addArgument] Cannot add a required argument after an optional one.');

    if ($argument->isArray())
      $this->hasAnArrayArgument = true;

    if ($argument->isRequired())
      ++$this->requiredCount;
    else
      $this->hasAnOptionalArgument = true;

    $this->arguments[$argument->getName()] = $argument;
  }

  /**
   * Returns an argument by name.
   *
   * @param string $name The argument name
   *
   * @return nbArgument A nbArgument object
   */
  public function getArgument($name)
  {
    if (!$this->hasArgument($name))
      throw new RangeException(sprintf('[nbArgumentSet::getArgument] The "%s" argument does not exist.', $name));

    return $this->arguments[$name];
  }

  /**
   * Returns true if an argument object exists by name.
   *
   * @param string $name The argument name
   *
   * @return Boolean true if the argument object exists, false otherwise
   */
  public function hasArgument($name)
  {
    return isset($this->arguments[$name]);
  }

  /**
   * Gets the array of nbArgument objects.
   *
   * @return array An array of nbArgument objects
   */
  public function getArguments()
  {
    return $this->arguments;
  }

  /**
   * Returns the number of arguments.
   *
   * @return integer The number of arguments
   */
  public function count()
  {
    return $this->hasAnArrayArgument ? PHP_INT_MAX : count($this->arguments);
  }

  /**
   * Returns the number of required arguments.
   *
   * @return integer The number of required arguments
   */
  public function countRequired()
  {
    return $this->requiredCount;
  }

  /**
   * Gets the default values.
   *
   * @return array An array of default values
   */
  public function getValues()
  {
    $values = array();
    foreach ($this->arguments as $argument)
      $values[$argument->getName()] = $argument->getValue();

    return $values;
  }

  public function  __toString()
  {
    $result = '';
    foreach($this->arguments as $argument)
      $result .= ' ' . $argument;

    return $result;
  }
}

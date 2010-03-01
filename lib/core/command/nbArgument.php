<?php

/**
 * Represents a command line argument.
 *
 * @package    bee
 * @subpackage argument
 */
class nbArgument
{
  const OPTIONAL = 1;
  const REQUIRED = 2;
  const IS_ARRAY = 4;

  protected
    $name     = null,
    $mode     = null,
    $value    = null,
    $valueSet = false,
    $description     = '';

  /**
   * Constructor.
   *
   * @param string  $name    The argument name
   * @param integer $mode    The argument mode: self::REQUIRED or self::OPTIONAL
   * @param string  $description    A description text
   * @param mixed   $default The default value (for self::OPTIONAL mode only)
   */
  public function __construct($name, $mode = self::OPTIONAL, $description = '', $default = null)
  {
    if (is_string($mode) || $mode > 7)
      throw new InvalidArgumentException('[nbArgument::__construct] Argument mode "%s" is not valid.');

    $this->name = $name;
    $this->mode = $mode;
    $this->description = $description;

    if(!($this->isOptional() || $this->isRequired()))
      $this->mode = nbArgument::OPTIONAL | $this->mode;
    
    if($this->isOptional() && $this->isRequired())
      throw new InvalidArgumentException('[nbArgument::__construct] Argument can\'t be required and optional at the same time.');

    if($this->isRequired() && $default !== null)
      throw new InvalidArgumentException('[nbArgument::__construct] Cannot set a default value to a required argument.');

    if($this->isOptional())
      $this->setValue($default);
  }

  /**
   * Returns the argument name.
   *
   * @return string The argument name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Returns true if the argument is required.
   *
   * @return Boolean true if parameter mode is self::REQUIRED, false otherwise
   */
  public function isRequired()
  {
    return self::REQUIRED === (self::REQUIRED & $this->mode);
  }

  /**
   * Returns true if the argument is optional.
   *
   * @return Boolean true if parameter mode is self::OPTIONAL, false otherwise
   */
  public function isOptional()
  {
    return self::OPTIONAL === (self::OPTIONAL & $this->mode);
  }

  /**
   * Returns true if the argument can take multiple values.
   *
   * @return Boolean true if mode is self::IS_ARRAY, false otherwise
   */
  public function isArray()
  {
    return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
  }

  /**
   * Sets the value.
   *
   * @param mixed The value
   */
  public function setValue($value)
  {
    if ($this->isArray()) {
      if (null === $value)
        $value = array();
      else if (!is_array($value))
        throw new InvalidArgumentException('[nbArgument::setValue] A default value for an array argument must be an array.');
    }

    $this->valueSet = true;
    $this->value = $value;
  }

  /**
   * Returns the value.
   *
   * @return mixed The value
   */
  public function getValue()
  {
    if ($this->isRequired() && !$this->valueSet)
      throw new LogicException('[nbArgument::getValue] Required argument has not been set.');

    return $this->value;
  }

  /**
   * Returns the description text.
   *
   * @return string The description text
   */
  public function getDescription()
  {
    return $this->description;
  }

  public function  __toString()
  {
    if($this->isArray()) {
      if($this->isOptional())
        return sprintf('[%s1] ... [%sN]', $this->getName(), $this->getName());

      return sprintf('%s1 ... %sN', $this->getName(), $this->getName());
    }

    if($this->isOptional())
      return sprintf('[%s]', $this->getName());

    return $this->getName();
  }
}

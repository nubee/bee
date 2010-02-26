<?php

/**
 * Represents a command line argument.
 *
 * @package    bee
 * @subpackage argument
 */
class nbArgument
{
  const REQUIRED = 1;
  const OPTIONAL = 2;
  const IS_ARRAY = 4;

  protected
    $name    = null,
    $mode    = null,
    $default = null,
    $help    = '';

  /**
   * Constructor.
   *
   * @param string  $name    The argument name
   * @param integer $mode    The argument mode: self::REQUIRED or self::OPTIONAL
   * @param string  $help    A help text
   * @param mixed   $default The default value (for self::OPTIONAL mode only)
   */
  public function __construct($name, $mode = null, $help = '', $default = null)
  {
    if (null === $mode)
      $mode = self::OPTIONAL;
    else if (is_string($mode) || $mode > 7)
      throw new InvalidArgumentException('Argument mode "%s" is not valid.');

    $this->name = $name;
    $this->mode = $mode;
    $this->help = $help;

    $this->setDefault($default);
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
   * Returns true if the argument can take multiple values.
   *
   * @return Boolean true if mode is self::IS_ARRAY, false otherwise
   */
  public function isArray()
  {
    return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
  }

  /**
   * Sets the default value.
   *
   * @param mixed $default The default value
   */
  public function setDefault($default = null)
  {
    if (self::REQUIRED === $this->mode && null !== $default)
      throw new InvalidArgumentException('Cannot set a default value except for nbArgument::OPTIONAL mode.');

    if ($this->isArray())
    {
      if (null === $default)
        $default = array();
      else if (!is_array($default))
        throw new InvalidArgumentException('A default value for an array argument must be an array.');
    }

    $this->default = $default;
  }

  /**
   * Returns the default value.
   *
   * @return mixed The default value
   */
  public function getDefault()
  {
    return $this->default;
  }

  /**
   * Returns the help text.
   *
   * @return string The help text
   */
  public function getHelp()
  {
    return $this->help;
  }
}

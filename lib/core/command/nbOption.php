<?php

/**
 * Represents a command line option.
 *
 * @package    bee
 * @subpackage command
 */
class nbOption {

  const PARAMETER_NONE = 1;
  const PARAMETER_OPTIONAL = 2;
  const PARAMETER_REQUIRED = 4;
  const IS_ARRAY = 8;

  private $name,
    $shortcut,
    $mode,
    $description,
    $value,
    $valueSet = false;

  /**
   * Constructor.
   *
   * @param string  $name         The option name
   * @param string  $shortcut     The shortcut (can be null)
   * @param integer $mode         The option mode: self::PARAMETER_REQUIRED, self::PARAMETER_NONE or self::PARAMETER_OPTIONAL
   * @param string  $description  The description
   * @param mixed   $default      The default value (must be null for self::PARAMETER_REQUIRED or self::PARAMETER_NONE)
   */
  function __construct($name, $shortcut = '', $mode = self::PARAMETER_NONE, $description = '', $default = null)
  {
    if(!isset($name))
      throw new InvalidArgumentException("[nbOption::__construct] Undefined name");
    if(strlen($name) < 3)
      throw new InvalidArgumentException("[nbOption::__construct] Name too short");
    if(!is_string($shortcut))
      throw new InvalidArgumentException("[nbOption::__construct] Shortcut must be a string");
    if(strlen($shortcut) > 1)
      throw new InvalidArgumentException("[nbOption::__construct] Shortcut too long");

    $this->name = $name;
    $this->shortcut = $shortcut;
    $this->description = $description;

    if($mode === self::IS_ARRAY)
      $mode |= self::PARAMETER_NONE;

    if($this->checkMode($mode))
      $this->mode = $mode;

    if($default && !$this->hasOptionalParameter())
      throw new InvalidArgumentException("[nbOption::__construct] Can\'t set default value if option parameter is not optional.");
    
    if($this->hasOptionalParameter())
      $this->setDefault($default);
  }

  /**
   * Returns the name.
   *
   * @return string The name.
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Returns the shortcut.
   *
   * @return string The shortcut
   */
  public function getShortcut()
  {
    return $this->shortcut;
  }

  /**
   * Returns true if  the option has the shortcut.
   *
   * @return Boolean true if option has shortcut, false otherwise
   */
  public function hasShortcut()
  {
    return (1 == strlen($this->shortcut));
  }

<<<<<<< HEAD
  /**
   * Returns the description.
   *
   * @return string The description.
   */
  public function getDescritpion()
=======
  public function getDescription()
>>>>>>> afd0ab556a88cfd5e044642fd8a8be00c6621254
  {
    return $this->description;
  }

  /**
   * Returns true if  the option has parameter.
   *
   * @return Boolean true if option has parameter, false otherwise
   */
  public function hasParameter()
  {
    return $this->hasOptionalParameter() || $this->hasRequiredParameter();
  }

  /**
   * Returns true if the option takes an optional parameter.
   *
   * @return Boolean true if parameter mode is self::PARAMETER_OPTIONAL, false otherwise
   */
  public function hasOptionalParameter()
  {
    return self::PARAMETER_OPTIONAL === ($this->mode & self::PARAMETER_OPTIONAL);
  }

 /**
   * Returns true if the option requires a parameter.
   *
   * @return Boolean true if parameter mode is self::PARAMETER_REQUIRED, false otherwise
   */
   public function hasRequiredParameter()
  {
    return self::PARAMETER_REQUIRED === ($this->mode & self::PARAMETER_REQUIRED);
  }

  /**
   * Returns true if the option can take multiple values.
   *
   * @return Boolean true if mode is self::IS_ARRAY, false otherwise
   */
  public function isArray()
  {
    return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
  }

  /**
   * Returns the value.
   *
   * @return mixed the value.
   */
  public function getValue()
  {
    if(!$this->valueSet)
      throw new LogicException(sprintf('[nbOption::getValue] Option %s value not set', $this->getName()));
/*    if(!$this->hasParameter())
      throw new LogicException('Option has PARAMETER_NONE mode');
    if($this->hasRequiredParameter() && null === $this->value)
      throw new LogicException('Option value not set');*/
    return $this->value;
  }

  /**
   * Sets the value.
   *
   * @param mixed $value The value.
   */
  public function setValue($value)
  {
    if(!$this->hasParameter())
      throw new LogicException('[nbOption::setValue] Option has no parameter');
    
    if($this->isArray() && !(is_array($value) || is_null($value)))
      throw new InvalidArgumentException('[nbOptionValue::setValue] Value must be an array or null');

    $this->valueSet = true;
    $this->value = $value;
  }

  /**
   * Sets the default value.
   *
   * @param mixed $default The default value
   */
  private function setDefault($value)
  {
    if(!$this->hasParameter() && null !== $value)
      throw new InvalidArgumentException('Couldn\'t set default value for option with PARAMETER_NONE');
//    if($this->hasRequiredParameter() && null !== $value)
//      throw new InvalidArgumentException('Couldn\'t set default value for option with PARAMETER_REQUIRED');
    if($this->hasParameter())
      $this->setValue($value);
  }

  /**
   * Returns true if option mode is defined.
   *
   * @return Boolean true if option mode is defined, false otherwise
   */
  public function checkMode($mode)
  {
    if(is_string($mode)
        || self::IS_ARRAY === $mode
        || $mode > 15 ) {
      throw new InvalidArgumentException('Argument mode is not valid.');
    }
    
    return true;
  }

  public function  __toString()
  {
    $result = $this->hasShortcut() ? '-' . $this->getShortcut() . '|' : '';
    $result .= '--' . $this->getName();
    if($this->hasOptionalParameter())
      $result .= sprintf('=[%s]', strtoupper($this->getName()));

    if($this->hasRequiredParameter())
      $result .= sprintf('=%s', strtoupper($this->getName()));

    if($this->isArray())
      return sprintf('[%s] ... [%s]', $result, $result);

    return '[' . $result . ']';
  }
}
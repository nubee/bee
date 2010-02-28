<?php
class nbOption {

  const PARAMETER_NONE = 1;
  const PARAMETER_OPTIONAL = 2;
  const PARAMETER_REQUIRED = 4;
  const IS_ARRAY = 8;

  private $name, $shortcut, $mode, $description ,$value;

  function __construct($name, $shortcut = '', $mode = self::PARAMETER_NONE, $description = '', $default = null)
  {
    if(! isset ($name))
      throw new InvalidArgumentException("Invalid argument: name");
    $this->name = $name;
    $this->shortcut = $shortcut;
    $this->description = $description;

    if($this->checkMode($mode))
      $this->mode = $mode;
    $this->setDefault($default);
  }

  public function getName()
  {
    return $this->name;
  }

  public function getShortcut()
  {
    return $this->shortcut;
  }

  public function getDescritpion()
  {
    return $this->description;
  }

  public function hasParameter()
  {
    return $this->hasOptionalParameter() || $this->hasRequiredParameter();
  }

  public function hasOptionalParameter()
  {
    return self::PARAMETER_OPTIONAL === ($this->mode & self::PARAMETER_OPTIONAL);
  }

  public function hasRequiredParameter()
  {
    return self::PARAMETER_REQUIRED === ($this->mode & self::PARAMETER_REQUIRED);
  }

  public function isArray()
  {
    return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
  }

  public function getValue()
  {
    if(! $this->hasParameter())
      throw new LogicException('Option has PARAMETER_NONE mode');
    if($this->hasRequiredParameter() && null === $this->value)
      throw new LogicException('Option value not set');
    return $this->value;
  }

  public function setValue($value)
  {
    if(! $this->hasParameter())
      throw new LogicException('Option has not parameter');
    if($this->isArray() && !(is_array($value) || is_null($value)))
      throw new InvalidArgumentException('Value must be an array or null');
    $this->value = $value;
  }

  public function setDefault($value)
  {
    if(!$this->hasParameter() && null !== $value)
      throw new InvalidArgumentException('Couldn\'t set default value for option with PARAMETER_NONE');
    if($this->hasRequiredParameter() && null !== $value)
      throw new InvalidArgumentException('Couldn\'t set default value for option with PARAMETER_REQUIRED');
    if($this->hasOptionalParameter())
      $this->setValue($value);
  }

  public function checkMode($mode)
  {
    if( is_string($mode) || self::IS_ARRAY === $mode ||
        (self::IS_ARRAY | self::PARAMETER_NONE) === $mode || $mode > 15 ) {

        throw new InvalidArgumentException('Argument mode is not valid.');
      }
      return true;
  }
}
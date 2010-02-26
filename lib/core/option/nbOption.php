<?php

class nbOption
{
  const PARAMETER_NONE     = 1;
  const PARAMETER_REQUIRED = 2;
  const PARAMETER_OPTIONAL = 4;
  const IS_ARRAY = 8;

  private $name, $shortcut, $mode, $value;

  public function  __construct($name, $shortcut = null, $mode = self::PARAMETER_NONE, $default = null )
  {
    if(null === $name || "" === $name)
    {
      throw new InvalidArgumentException("Option name is null or empty");
    }

    if(is_string($mode) || $mode > 15)
    {
      throw new InvalidArgumentException("Option accepts only defined modes");
    }

    $this->name = $name;
    $this->shortcut = $shortcut;
    $this->mode = $mode;
    $this->setDefault($default);
  }

  public function getShortcut()
  {
    return $this->shortcut;
  }
  public function hasOptionalParameter()
  {
    return self::PARAMETER_OPTIONAL === (self::PARAMETER_OPTIONAL & $this->mode);
  }

  public function hasRequiredParameter()
  {
    return self::PARAMETER_REQUIRED === (self::PARAMETER_REQUIRED & $this->mode);
  }

  public function isArray()
  {
    return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
  }

  public function setValue($v)
  {
    $this->value = $v;
  }

  public function getValue()
  {
    return $this->value;
  }

  private function setDefault($default) {
    if(null === $default)
      return;
    if($this->hasOptionalParameter())
      $this->value = $default;
    else
      throw new LogicException("Can't pass default value for non optional parameter");
  }
}

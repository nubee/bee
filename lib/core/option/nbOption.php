<?php

class nbOption
{
  const PARAMETER_NONE     = 1;
  const PARAMETER_REQUIRED = 2;
  const PARAMETER_OPTIONAL = 4;

  private $name, $shortcut, $mode;

  public function  __construct($name, $shortcut = null, $mode = nbOption::PARAMETER_NONE )
  {
    if(null === $name || "" === $name)
    {
      throw new InvalidArgumentException("Option name is null or empty");
    }

    if(is_string($mode) || $mode > 7)
    {
      throw new InvalidArgumentException("Option accepts only defined modes");
    }

    $this->name = $name;
    $this->shortcut = $shortcut;
    $this->mode = $mode;
  }

  public function getShortcut()
  {
    return $this->shortcut;
  }
  public function hasOptionalParameter()
  {
    return (nbOption::PARAMETER_OPTIONAL === $this->mode);
  }

  public function hasRequiredParameter()
  {
    return (nbOption::PARAMETER_REQUIRED === $this->mode);
  }

}

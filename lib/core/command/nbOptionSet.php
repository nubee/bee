<?php
class nbOptionSet {

  private $options = array(),$shortcuts = array(), $requiredCount = 0;

  function __construct($options = array())
  {
    $this->addOptions($options);
  }

  public function count()
  {
    return count($this->options);
  }

  public function countRequired()
  {
    return $this->requiredCount;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function hasOption($optionName)
  {
    return array_key_exists($optionName, $this->options) || array_key_exists($optionName, $this->shortcuts);
  }

  public function hasShortcut($shortcut)
  {
    foreach($this->options as $option)
      if($option->hasShortcut($shortcut) && $option->getShortcut() == $shortcut)
        return true;

    return false;
  }

  public function getByShortcut($shortcut)
  {
    foreach($this->options as $option)
      if($option->hasShortcut($shortcut) && $option->getShortcut() == $shortcut)
        return $option;

    throw new RangeException(sprintf('[nbOptionSet::getByShortcut] Option with shortcut %s does not exist.', $shortcut));
  }


  public function addOptions($options = array())
  {
    if(! is_array($options))
      throw new InvalidArgumentException("first argument must be an array");
    foreach ($options as $option)
      $this->addOption($option);
  }

  public function addOption(nbOption $option)
  {
    if($this->hasOption($option->getName()))
      throw new InvalidArgumentException(sprintf("option %s already exists",$option->getName()));
    if($this->hasOption($option->getShortcut()))
      throw new InvalidArgumentException(sprintf("shortcut %s already registered",$option->getShortcut()));
    $this->options[$option->getName()] = $option;
    if($option->hasShortcut())
      $this->shortcuts[$option->getShortcut()] = $option;
    if($option->hasRequiredParameter())
      ++$this->requiredCount;
  }

  public function getOption($optionName)
  {
    if(!$this->hasOption($optionName))
      throw new RangeException(sprintf('option %s doesn\'t exist',$optionName));
    if(strlen($optionName) > 1)
      return $this->options[$optionName];
    else
      return $this->shortcuts[$optionName];
  }

  public function getValues() {
    $res = array();
    foreach ($this->options as $option) {
      $res[$option->getName()] = $option->getValue();
    }
    return $res;
  }

  public function  __toString()
  {
    $result = '';
    foreach($this->options as $option)
      $result .= ' ' . $option;

    return $result;
  }
}
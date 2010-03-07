<?php

/**
 * Represent a set of command line options.
 *
 * @package    bee
 * @subpackage command
 */
class nbOptionSet {

  private
    $options = array(),
    $shortcuts = array(),
    $requiredCount = 0;

  /**
   * Constructor.
   *
   * @param array $options An array of sfCommandOption objects
   */
  function __construct($options = array())
  {
    $this->addOptions($options);
  }

  /**
   * Returns the number of options.
   *
   * @return integer The number of options.
   */
  public function count()
  {
    return count($this->options);
  }

  /**
   * Returns the number of options required.
   *
   * @return integer The number of options required.
   */
  public function countRequired()
  {
    return $this->requiredCount;
  }

  /**
   * Gets the array of nbOption objects.
   *
   * @return array An array of nbOption objects
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Returns true if an option object exists by name.
   *
   * @param string $optionName The option name
   *
   * @return Boolean true if the option object exists, false otherwise
   */
  public function hasOption($optionName)
  {
    return array_key_exists($optionName, $this->options) || array_key_exists($optionName, $this->shortcuts);
  }

  /**
   * Returns true if an option object exists by shortcut.
   *
   * @param string $shortcut The option shortcut
   *
   * @return Boolean true if the option object exists, false otherwise
   */
  public function hasShortcut($shortcut)
  {
    foreach($this->options as $option)
      if($option->hasShortcut($shortcut) && $option->getShortcut() == $shortcut)
        return true;

    return false;
  }

  /**
   * Gets an option by shortcut.
   *
   * @param string $shortcut The shortcut
   *
   * @return nbOption A nbOption object
   */
  public function getByShortcut($shortcut)
  {
    foreach($this->options as $option)
      if($option->hasShortcut($shortcut) && $option->getShortcut() == $shortcut)
        return $option;

    throw new RangeException(sprintf('[nbOptionSet::getByShortcut] Option with shortcut %s does not exist.', $shortcut));
  }

  /**
   * Add an array of nbOption objects.
   *
   * @param array $options An array of nbOption objects
   */
  public function addOptions($options = array())
  {
    if(! is_array($options))
      throw new InvalidArgumentException("[nbOptionSet::addOptions] First argument must be an array");
    foreach ($options as $option)
      $this->addOption($option);
  }

  /**
   * Merges two option sets.
   *
   * @param array $set A nbOptionSet object
   */
  public function mergeOptions(nbOptionSet $set)
  {
    foreach($set->options as $option)
      $this->addOption($option);
  }

  /**
   * Add a nbOption object.
   *
   * @param nbOption $option A nbOption object
   */
  public function addOption(nbOption $option)
  {
    if($this->hasOption($option->getName()))
      throw new InvalidArgumentException(sprintf('[nbOptionSet::addOption] Option "%s" already exists', $option->getName()));
    if($this->hasOption($option->getShortcut()))
      throw new InvalidArgumentException(sprintf('[nbOptionSet::addOption] Shortcut "%s" already registered', $option->getShortcut()));
    
    $this->options[$option->getName()] = $option;
    if($option->hasShortcut())
      $this->shortcuts[$option->getShortcut()] = $option;
    if($option->hasRequiredParameter())
      ++$this->requiredCount;
  }

  /**
   * Returns an option by name.
   *
   * @param string $optionName The option name
   *
   * @return nbOption A nbOption object
   */
  public function getOption($optionName)
  {
    if(!$this->hasOption($optionName))
      throw new RangeException(sprintf('[nbOptionSet::getOption] Option "%s" doesn\'t exist', $optionName));

    return (strlen($optionName) > 1) 
      ? $this->options[$optionName]
      : $this->shortcuts[$optionName];
  }

  /**
   * Gets an array of default values.
   *
   * @return array An array of all default values
   */
  public function getDefaultValues() {
    $res = array();
    foreach ($this->options as $option) {
      if($option->hasOptionalParameter())
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
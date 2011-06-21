<?php

/**
 * Class to manage command line arguments and options.
 *
 * @package    bee
 * @subpackage command
 */
class nbCommandLineParser {

  protected
  $errors = array(),
  $options = null,
  $arguments = array(),
  $optionValues = array(),
  $argumentValues = array(),
  $parsedArgumentValues = array(),
  $parsedLongOptionValues = array(),
  $parsedShortOptionValues = array();

  /**
   * Constructor.
   *
   * @param nbArgumentSet $arguments A nbArgumentSet object
   * @param nbOptionSet   $options   A setOptions object
   */
  public function __construct(array $arguments = array(), array $options = array()) {
    $this->setArguments($arguments);
    $this->setOptions($options);
  }

  /**
   * Sets the argument set.
   *
   * @param nbArgumentSet $arguments A nbArgumentSet object
   */
  public function setArguments(array $arguments) {
    $this->arguments = new nbArgumentSet($arguments);
  }

  /**
   * Adds to the argument set.
   *
   * @param array $arguments An array of arguments
   */
  public function addArguments(nbArgumentSet $arguments) {
    $this->arguments->mergeArguments($arguments);
  }

  /**
   * Gets the argument set.
   *
   * @return nbArgumentSet A nbArgumentSet object
   */
  public function getArguments() {
    return $this->arguments;
  }

  /**
   * Sets the option set.
   *
   * @param nbOptionSet $options A nbOptionSet object
   */
  public function setOptions(array $options) {
    $this->options = new nbOptionSet($options);
  }

  /**
   * Adds to the option set.
   *
   * @param array $options An array of options
   */
  public function addOptions(nbOptionSet $options) {
    //print_r($this->options->getOptions());
    $this->options->mergeOptions($options);
  }

  /**
   * Gets the option set.
   *
   * @return nbOptionSet A nbOptionSet object
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Parses command line arguments.
   *
   * @param mixed $arguments A string or an array of command line parameters
   */
  public function parse($commandLine = null, $namespace = '', $commandName = '') {
    if (null === $commandLine) {
      $this->commandLineTokens = $_SERVER['argv'];

      // we strip command line program
      if (count($this->commandLineTokens) && '-' != $this->commandLineTokens[0][0])
        array_shift($this->commandLineTokens);
    }
    else if (!is_array($commandLine)) {
      $commandLine = trim($commandLine);
      // hack to split arguments with spaces : --test="with some spaces"
      $commandLine = preg_replace('/(\'|")(.+?)\\1/e', "str_replace(' ', '__PLACEHOLDER__', '\\2')", $commandLine);
      $this->commandLineTokens = preg_split('/\s+/', $commandLine);
      $this->commandLineTokens = str_replace('__PLACEHOLDER__', ' ', $this->commandLineTokens);
    }
    else
      $this->commandLineTokens = $commandLine;

    $this->argumentValues = array();
    $this->optionValues = array();
    $this->parsedArgumentValues = array();
    $this->errors = array();

    // get default values for optional arguments
    $this->argumentValues = $this->arguments->getDefaultValues();

    // parse option and arguments from $commandLineArguments
    while (!in_array($argument = array_shift($this->commandLineTokens), array('', null))) {
      if ('--' == $argument) {
        // stop options parsing
        //$this->parsedArgumentValues = array_merge($this->parsedArgumentValues, $this->commandLineArguments);
        $this->commandLineTokens = array();
        break;
      }

      if ('--' == substr($argument, 0, 2))
        $this->parseLongOption(substr($argument, 2));
      else if ('-' == $argument[0])
        $this->parseShortOption(substr($argument, 1));
      else
        $this->parsedArgumentValues[] = $argument;
    }

    // if is set option config-file set all arguments and parameters declared in the configuration file
    if (isset($this->parsedLongOptionValues['config-file'])) {
      if ($this->parsedLongOptionValues['config-file'][0] === true) {
        // get default config file
        $configFile = $this->options->getOption('config-file')->getValue();
      }
      else
        $configFile = $this->parsedLongOptionValues['config-file'][0];
      $configParser = new nbYamlConfigParser();
      $configParser->parseFile($configFile);
      $path_yml = $namespace . '_' . $commandName;
      if (nbConfig::has($path_yml)) {
        $configurationValues = nbConfig::get($path_yml);
        foreach ($configurationValues as $name => $value) {
          if (!$this->getArguments()->hasArgument($name)
                  || ('' == $value))
            continue;
          $this->argumentValues[$name] = $value;
        }
        foreach ($configurationValues as $name => $value) {
          if (!$this->getOptions()->hasOption($name))
            continue;
          $option = $this->getOptions()->getOption($name);
          if ($value !== false && $value !== null)
            $this->setOption($option, $value);
        }
      }
    }

    //set argumentValues parsed from command line
    $position = 0;
    foreach ($this->arguments->getArguments() as $argument) {
      if (array_key_exists($position, $this->parsedArgumentValues)) {
        if ($argument->isArray()) {
          $this->argumentValues[$argument->getName()] = array_slice($this->parsedArgumentValues, $position);
          break;
        }
        else
          $this->argumentValues[$argument->getName()] = $this->parsedArgumentValues[$position];
      }
      ++$position;
    }

    // set long option values parsed from command line
    foreach ($this->options->getOptions() as $option) {
      $name = $option->getName();
      if (isset($this->parsedLongOptionValues[$name]))
        foreach ($this->parsedLongOptionValues[$name] as $key => $value)
          $this->setOption($option, $value);
      else if ($option->hasShortcut() && isset($this->parsedShortOptionValues[$option->getShortcut()]))
        foreach ($this->parsedShortOptionValues[$option->getShortcut()] as $key => $value)
          $this->setOption($option, $value);
    }
    // get default values for option with optional parameter not set
    foreach ($this->getOptionValues() as $optionName => $optionValue) {
      $option = $this->options->getOption($optionName);
      if ($option->hasOptionalParameter() && ($this->optionValues[$optionName] == '' || $this->optionValues[$optionName] === true)) {
        $this->setOption($option, $option->getValue());
      }
    }
  }

  /**
   * Returns true if the current command line options validate the argument and option sets.
   *
   * @return true if there are some validation errors, false otherwise
   */
  public function isValid() {
    foreach ($this->arguments->getArguments() as $argument) {
      if ($argument->isRequired()) {
        if (!isset($this->argumentValues[$argument->getName()]) || $this->argumentValues[$argument->getName()] == '')
          $this->errors[] = 'Not enough arguments. ' . $argument->getName() . " missing";
      }
    }
    if (count($this->parsedArgumentValues) > $this->arguments->count())
      $this->errors[] = sprintf('Too many arguments ("%s" given).', implode(' ', $this->parsedArgumentValues));

    foreach ($this->parsedLongOptionValues as $optionName => $optionValues) {
      if (!$this->getOptions()->hasOption($optionName)) {
        $this->errors[] = sprintf('The "--%s" option does not exist.', $optionName);
      }
    }

    foreach ($this->parsedShortOptionValues as $shortcut => $optionValues) {
      if (!$this->getOptions()->hasShortcut($shortcut)) {
        $this->errors[] = sprintf('The "--%s" option does not exist.', $shortcut);
      }
    }

    foreach ($this->getOptionValues() as $optionName => $optionValue) {
      $option = $this->getOptions()->getOption($optionName);
      if (!$option->hasParameter() && !($this->getOptionValue($optionName) === true || $this->getOptionValue($optionName) == '')) {
        $this->errors[] = sprintf('Option "--%s" does not take an argument.', $optionName);
      }
      if ($option->hasRequiredParameter() && ($this->getOptionValue($optionName) === true || $this->getOptionValue($optionName) == '')) {
        $this->errors[] = sprintf('Option "--%s" requires an argument.', $optionName);
      }
    }
    return count($this->errors) ? false : true;
  }

  /**
   * Gets the current errors.
   *
   * @return array An array of errors
   */
  public function getErrors() {
    return $this->errors;
  }

  /**
   * Returns the argument values.
   *
   * @return array An array of argument values
   */
  public function getArgumentValues() {
    return $this->argumentValues;
  }

  /**
   * Returns the argument value for a given argument name.
   *
   * @param string $name The argument name
   *
   * @return mixed The argument value
   */
  public function getArgumentValue($name) {
    if (!$this->hasArgumentValue($name))
      throw new RangeException(sprintf('[nbCommandLineParser::getArgumentValue] The "%s" argument does not exist.', $name));

    return $this->argumentValues[$name];
  }

  /**
   * Returns true if the argument exists.
   *
   * @param string $name The argument name
   *
   * @return Boolean true if the argument exists
   */
  public function hasArgumentValue($name) {
    return isset($this->argumentValues[$name]);
  }

  /**
   * Returns the options values.
   *
   * @return array An array of option values
   */
  public function getOptionValues() {
    return $this->optionValues;
  }

  /**
   * Returns the option value for a given option name.
   *
   * @param string $name The option name
   *
   * @return mixed The option value
   */
  public function getOptionValue($name) {
    if (!$this->hasOptionValue($name))
      throw new RangeException(sprintf('The "%s" option does not exist.', $name));

    return $this->optionValues[$name];
  }

  /**
   * Returns true if the option exists.
   *
   * @param string $name The option name
   *
   * @return Boolean true if the option exists
   */
  public function hasOptionValue($name) {
    return isset($this->optionValues[$name]);
  }

  /**
   * Parses a short option.
   *
   * @param string $argument The option argument
   */
  protected function parseShortOption($argument) {

    // short option must be followed by space
    // short option can be aggregated like in -vd (== -v -d)
    for ($i = 0, $count = strlen($argument); $i < $count; ++$i) {
      $shortcut = $argument[$i];
      $value = true;
      // take next element as argument (if it doesn't start with a -)
      if ($this->options->hasShortcut($shortcut) && $this->options->getByShortcut($shortcut)->hasParameter()) {
        if (count($this->commandLineTokens) && '-' != $this->commandLineTokens[0][0]) {
          $value = array_shift($this->commandLineTokens);
        }
      }
      $this->parsedShortOptionValues[$shortcut][] = $value;
    }
  }

  /**
   * Parses a long option.
   *
   * @param string $argument The option argument
   */
  protected function parseLongOption($argument) {
    $name = '';
    $value = true;
    if (false !== strpos($argument, '=')) {
      list($name, $value) = explode('=', $argument, 2);
    } else {
      $name = $argument;
    }
    $this->parsedLongOptionValues[$name][] = $value;
  }

  public function setOption(nbOption $option, $value) {
    if ($option->isArray())
      $this->optionValues[$option->getName()][] = $value;
    else
      $this->optionValues[$option->getName()] = $value;
  }

}

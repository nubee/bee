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
  $parsedArgumentValues = array();

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
      $this->commandLineArguments = $_SERVER['argv'];

      // we strip command line program
      if (isset($this->commandLineArguments[0]) && '-' != $this->commandLineArguments[0][0])
        array_shift($this->commandLineArguments);
    }
    else if (!is_array($commandLine)) {
      // hack to split arguments with spaces : --test="with some spaces"
      $commandLine = preg_replace('/(\'|")(.+?)\\1/e', "str_replace(' ', '__PLACEHOLDER__', '\\2')", $commandLine);
      $this->commandLineArguments = preg_split('/\s+/', $commandLine);
      $this->commandLineArguments = str_replace('__PLACEHOLDER__', ' ', $this->commandLineArguments);
    }
    else
      $this->commandLineArguments = $commandLine;

//    $this->optionValues         = $this->options->getDefaultValues();
    $this->argumentValues = $this->arguments->getDefaultValues();
    $this->parsedArgumentValues = array();
    $this->errors = array();

    while (!in_array($argument = array_shift($this->commandLineArguments), array('', null))) {
      if ('--' == $argument) {
        // stop options parsing
        //$this->parsedArgumentValues = array_merge($this->parsedArgumentValues, $this->commandLineArguments);
        $this->commandLineArguments = array();
        break;
      }

      if ('--' == substr($argument, 0, 2))
        $this->parseLongOption(substr($argument, 2));
      else if ('-' == $argument[0])
        $this->parseShortOption(substr($argument, 1));
      else
        $this->parsedArgumentValues[] = $argument;
    }

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

    if (isset($this->optionValues['config-file'])) {
      $configParser = new nbYamlConfigParser();
      $configParser->parseFile($this->optionValues['config-file']);
      $path_yml = $namespace . '_' . $commandName;
      if (nbConfig::has($path_yml)) {
        $configurationValues = nbConfig::get($path_yml);
        foreach ($configurationValues as $name => $value) {
          if (!$this->getArguments()->hasArgument($name)
                  || ('' == $value)
                  || isset($this->argumentValues[$name]))
            continue;
          $this->argumentValues[$name] = $value;
        }
        foreach ($configurationValues as $name => $value) {
          if (!$this->getOptions()->hasOption($name)
                  || ('' == $value)
                  || isset($this->optionValues[$name]))
            continue;
          $this->optionValues[$name] = $value;
        }
      }
    }
  }

  /**
   * Returns true if the current command line options validate the argument and option sets.
   *
   * @return true if there are some validation errors, false otherwise
   */
  public function isValid() {
    
    foreach ($this->arguments->getArguments() as $argument){
      if ($argument->isRequired()){
        if($this->argumentValues[$argument->getName()] == '')
          $this->errors[] = 'Not enough arguments. '.$argument->getName()." missing";            
      }
    }
    /*
    if (count($this->argumentValues) < $this->arguments->countRequired())
      $this->errors[] = 'Not enough arguments.';
    else*/ 
    if (count($this->parsedArgumentValues) > $this->arguments->count()) 
      $this->errors[] = sprintf('Too many arguments ("%s" given).', implode(' ', $this->parsedArgumentValues));
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

      if (!$this->options->hasShortcut($shortcut)) {
        $this->errors[] = sprintf('The option "-%s" does not exist.', $shortcut);
        continue;
      }

      $option = $this->options->getByShortcut($shortcut);

      // required argument?
      if ($option->hasRequiredParameter()) {
        // take next element as argument (if it doesn't start with a -)
        if (count($this->commandLineArguments) && $this->commandLineArguments[0][0] != '-') {
          $value = array_shift($this->commandLineArguments);
          $this->setOption($option, $value);
        }
        else
          $this->errors[] = sprintf('Option "-%s" requires an argument', $shortcut);

        continue;
      }
      else if ($option->hasOptionalParameter()) {
        // take next element as argument (if it doesn't start with a -)
        if (count($this->commandLineArguments) && $this->commandLineArguments[0][0] != '-') {
          $value = array_shift($this->commandLineArguments);
          $this->setOption($option, $value);
        }
        else
          $this->setOption($option, $option->getValue());

        continue;
      }

      $this->setOption($option, true);
    }
  }

  /**
   * Parses a long option.
   *
   * @param string $argument The option argument
   */
  protected function parseLongOption($argument) {
    if (false !== strpos($argument, '=')) {
      list($name, $value) = explode('=', $argument, 2);

      if (!$this->options->hasOption($name)) {
        $this->errors[] = sprintf('The "--%s" option does not exist.', $name);
        return;
      }

      $option = $this->options->getOption($name);

      if (!$option->hasParameter()) {
        $this->errors[] = sprintf('Option "--%s" does not take an argument.', $name);
        $value = true;
      }
    } else {
      $name = $argument;

      if (!$this->options->hasOption($name)) {
        $this->errors[] = sprintf('The "--%s" option does not exist.', $name);
        return;
      }

      $option = $this->options->getOption($name);

      if ($option->hasRequiredParameter()) {
        $this->errors[] = sprintf('Option "--%s" requires an argument.', $name);
        return;
      }

      $value = $option->hasParameter() ? $option->getValue() : true;
    }

    $this->setOption($option, $value);
  }

  public function setOption(nbOption $option, $value) {
    if ($option->isArray())
      $this->optionValues[$option->getName()][] = $value;
    else
      $this->optionValues[$option->getName()] = $value;
  }

}

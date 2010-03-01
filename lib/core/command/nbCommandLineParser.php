<?php

/**
 * Class to manage command line arguments and options.
 *
 * @package    bee
 * @subpackage command
 */
class nbCommandLineParser
{
  protected
    $errors               = array(),
    $options              = null,
    $arguments            = array(),
    $optionValues         = array(),
    $argumentValues       = array(),
    $parsedArgumentValues = array();

  /**
   * Constructor.
   *
   * @param nbArgumentSet $arguments A nbArgumentSet object
   * @param nbOptionSet   $options   A setOptions object
   */
  public function __construct(nbArgumentSet $arguments = null, nbOptionSet $options = null)
  {
    if (null === $arguments)
      $arguments = new nbArgumentSet();

    $this->setArguments($arguments);

    if (null === $options)
      $options = new nbOptionSet();

    $this->setOptions($options);
  }

  /**
   * Sets the argument set.
   *
   * @param nbArgumentSet $arguments A nbArgumentSet object
   */
  public function setArguments(nbArgumentSet $arguments)
  {
    $this->arguments = $arguments;
  }

  /**
   * Adds to the argument set.
   *
   * @param array $arguments An array of arguments
   */
  public function addArguments(nbArgumentSet $arguments)
  {
    $this->arguments->mergeArguments($arguments);
  }

  /**
   * Gets the argument set.
   *
   * @return nbArgumentSet A nbArgumentSet object
   */
  public function getArguments()
  {
    return $this->arguments;
  }

  /**
   * Sets the option set.
   *
   * @param nbOptionSet $options A nbOptionSet object
   */
  public function setOptions(nbOptionSet $options)
  {
    $this->options = $options;
  }

  /**
   * Adds to the option set.
   *
   * @param array $options An array of options
   */
  public function addOptions(nbOptionSet $options)
  {
    $this->options->mergeOptions($options);
  }

  /**
   * Gets the option set.
   *
   * @return nbOptionSet A nbOptionSet object
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Parses command line arguments.
   *
   * @param mixed $arguments A string or an array of command line parameters
   */
  public function parse($commandLine = null)
  {
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

//    $this->arguments            = $arguments;
//    $this->optionValues         = $this->options->getValues();
//    $this->argumentValues       = $this->arguments->getValues();
    $this->parsedArgumentValues = array();
    $this->errors               = array();

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

    //$this->arguments = $arguments;

    if (count($this->parsedArgumentValues) < $this->arguments->countRequired())
      $this->errors[] = 'Not enough arguments.';
    else if (count($this->parsedArgumentValues) > $this->arguments->count())
      $this->errors[] = sprintf('Too many arguments ("%s" given).', implode(' ', $this->parsedArgumentValues));
  }

  /**
   * Returns true if the current command line options validate the argument and option sets.
   *
   * @return true if there are some validation errors, false otherwise
   */
  public function isValid()
  {
    return count($this->errors) ? false : true;
  }

  /**
   * Gets the current errors.
   *
   * @return array An array of errors
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Returns the argument values.
   *
   * @return array An array of argument values
   */
  public function getArgumentValues()
  {
    return $this->argumentValues;
  }

  /**
   * Returns the argument value for a given argument name.
   *
   * @param string $name The argument name
   *
   * @return mixed The argument value
   */
  public function getArgumentValue($name)
  {
    if (!$this->arguments->hasArgument($name))
      throw new RangeException(sprintf('The "%s" argument does not exist.', $name));

    return $this->argumentValues[$name];
  }

  /**
   * Returns the options values.
   *
   * @return array An array of option values
   */
  public function getOptionValues()
  {
    return $this->optionValues;
  }

  /**
   * Returns the option value for a given option name.
   *
   * @param string $name The option name
   *
   * @return mixed The option value
   */
  public function getOptionValue($name)
  {
    if (!$this->options->hasOption($name))
      throw new RangeException(sprintf('The "%s" option does not exist.', $name));

    return $this->optionValues[$name];
  }

  /**
   * Parses a short option.
   *
   * @param string $argument The option argument
   */
  protected function parseShortOption($argument)
  {
    // short option can be aggregated like in -vd (== -v -d)
    for ($i = 0, $count = strlen($argument); $i < $count; ++$i) {
      $shortcut = $argument[$i];
      $value    = true;

      if (!$this->options->hasShortcut($shortcut)) {
        $this->errors[] = sprintf('The option "-%s" does not exist.', $shortcut);
        continue;
      }

      $option = $this->options->getByShortcut($shortcut);

      // required argument?
      if ($option->hasRequiredParameter()) {
        if ($i + 1 < strlen($argument)) {
          $value = substr($argument, $i + 1);
          $this->setOption($option, $value);
          break;
        }
        else {
          // take next element as argument (if it doesn't start with a -)
          if (count($this->commandLineArguments) && $this->commandLineArguments[0][0] != '-') {
            $value = array_shift($this->commandLineArguments);
            $this->setOption($option, $value);
            break;
          }
          else {
            $this->errors[] = sprintf('Option "-%s" requires an argument', $shortcut);
            $value = null;
          }
        }
      }
      else if ($option->hasOptionalParameter()) {
        if (substr($argument, $i + 1) != '')
          $value = substr($argument, $i + 1);
        else {
          // take next element as argument (if it doesn't start with a -)
          if (count($this->commandLineArguments) && $this->commandLineArguments[0][0] != '-')
            $value = array_shift($this->commandLineArguments);
          else
            $value = $option->getValue();
        }

        $this->setOption($option, $value);
        break;
      }

      $this->setOption($option, $value);
    }
  }

  /**
   * Parses a long option.
   *
   * @param string $argument The option argument
   */
  protected function parseLongOption($argument)
  {
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
    }
    else {
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

  public function setOption(nbOption $option, $value)
  {
    if ($option->isArray())
      $this->optionValues[$option->getName()][] = $value;
    else
      $this->optionValues[$option->getName()] = $value;
  }
}

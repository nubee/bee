<?php

/**
 * Represents an application that executes commands.
 *
 * @package    bee
 * @subpackage application
 */
abstract class nbApplication
{
  protected
    $name = 'UNDEFINED',
    $version = 'UNDEFINED',
    $arguments = null,
    $options = null,
    $commands = null,
    $verbose = false,
    $trace = false;

  public function __construct()
  {
    $this->arguments = new nbArgumentSet();
    $this->options = new nbOptionSet();
    $this->commands = new nbCommandSet();
    
    $this->arguments->addArgument(
      new nbArgument('command', nbArgument::REQUIRED, 'The command to execute')
    );
    $this->options->addOptions(array(
      new nbOption('version', 'V', nbOption::PARAMETER_NONE, 'Shows the version'),
      new nbOption('verbose', 'v', nbOption::PARAMETER_NONE, 'Set verbosity'),
      new nbOption('trace', 't', nbOption::PARAMETER_NONE, 'Shows exception trace')
    ));

    $this->configure();
  }

  public function run($commandLine = null)
  {
    $this->parser = new nbCommandLineParser($this->arguments, $this->options);
    $this->parser->parse($commandLine);

    if($this->handleOptions($this->parser->getOptionValues()))
      return;

    $commandName = 'list';
    if($this->parser->hasArgumentValue('command'))
      $commandName = $this->parser->getArgumentValue('command');

    $command = $this->commands->getCommand($commandName);
    $command->run($this->parser, $commandLine);
  }

  public function getName()
  {
    return $this->name;
  }
  
  public function getVersion()
  {
    return $this->version;
  }
  
  protected abstract function configure();

  protected function handleOptions(array $options)
  {
    $logger = nbLogger::getInstance();
    if(isset($options['verbose']))
      $this->verbose = true;

    if(isset($options['trace'])) {
      $this->verbose = true;
      $this->trace   = true;
    }

    if(isset($options['version'])) {
      $logger->log(($this->verbose ? $this->getName() . ' version ' : '') . $this->getVersion());
      return true;
    }

    return false;
  }

  public function addArguments(array $arguments)
  {
    $this->arguments->addArguments($arguments);
  }

  public function hasArguments()
  {
    return $this->arguments->count() > 0;
  }

  public function getArguments()
  {
    return $this->arguments;
  }

  public function addOptions(array $options)
  {
    $this->options->addOptions($options);
  }

  public function hasOptions()
  {
    return $this->options->count() > 0;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function setCommands(nbCommandSet $commands)
  {
    $this->commands = $commands;
  }

  public function hasCommands()
  {
    return $this->commands->count() > 0;
  }

  public function getCommands()
  {
    return $this->commands;
  }

  /**
   * Renders an exception.
   *
   * @param Exception $e An exception object
   */
  public function renderException(Exception $e)
  {
    $title = sprintf('  [%s]  ', get_class($e));
    $len = strlen($title);
    $lines = array();
    foreach (explode("\n", $e->getMessage()) as $line) {
      $lines[] = sprintf('  %s  ', $line);
      $len = max(strlen($line) + 4, $len);
    }

    $messages = array(str_repeat(' ', $len));

    if ($this->trace)
      $messages[] = $title . str_repeat(' ', $len - strlen($title));

    foreach ($lines as $line)
      $messages[] = $line . str_repeat(' ', $len - strlen($line));

    $messages[] = str_repeat(' ', $len);

    $output = new nbFileOutput(STDERR);
    $output->setFormatter(new nbAnsiColorFormatter());
    $logger = nbLogger::getInstance();
    $logger->setOutput($output);
    $logger->log("\n");
    foreach ($messages as $message) {
      $logger->log($message, 'error');
      $logger->log("\n");
    }

/*    if (null !== $this->currentTask && $e instanceof sfCommandArgumentsException) {
      fwrite(STDERR, $this->formatter->format(sprintf($this->currentTask->getSynopsis(), $this->getName()), 'INFO', STDERR)."\n");
      fwrite(STDERR, "\n");
    }*/

    if ($this->trace) {
      $logger->log("\n");
      $logger->log("Exception trace:\n", 'comment');

      // exception related properties
      $trace = $e->getTrace();
      $file = $e->getFile() != null ? $e->getFile() : 'n/a';
      $line = $e->getLine() != null ? $e->getLine() : 'n/a';

      $logger->log(sprintf(" %s:%s\n", $file, $line), 'info');

      for ($i = 0, $count = count($trace); $i < $count; ++$i) {
        $class = isset($trace[$i]['class']) ? $trace[$i]['class'] : '';
        $type = isset($trace[$i]['type']) ? $trace[$i]['type'] : '';
        $function = $trace[$i]['function'] . '()';
        $file = isset($trace[$i]['file']) ? $trace[$i]['file'] : 'n/a';
        $line = isset($trace[$i]['line']) ? $trace[$i]['line'] : 'n/a';

        $message = sprintf(" %s%s%s at %s:%s\n", $class, $type, $function, $file, $line);
        $logger->log($message, 'info');
      }
    }
  }
}

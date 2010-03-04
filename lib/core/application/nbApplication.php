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
    $trace = false,
    $logger = null;

  private $commandFiles = array();

  public function __construct(array $commands = null)
  {
    $this->logger = nbLogger::getInstance();
    $this->arguments = new nbArgumentSet();
    $this->options = new nbOptionSet();
    $this->commands = new nbCommandSet();
    
    $this->arguments->addArgument(
      new nbArgument('command', nbArgument::REQUIRED, 'The command to execute')
    );
    $this->options->addOptions(array(
      new nbOption('version', 'V', nbOption::PARAMETER_NONE, 'Shows the version'),
      new nbOption('verbose', 'v', nbOption::PARAMETER_NONE, 'Set verbosity'),
      new nbOption('trace', 't', nbOption::PARAMETER_NONE, 'Shows exception trace'),
      new nbOption('help', '?', nbOption::PARAMETER_NONE, 'Shows application help')
    ));

    $this->configure();

    $this->registerCommands($commands);
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
    $r = new ReflectionClass($command);
    if($r->isSubclassOf('nbApplicationCommand'))
      $command->setApplication($this);
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

  public function formatHelpString($name, $argumentSet, $optionSet, $description)
  {
    $max = 0;
    foreach($argumentSet->getArguments() as $argument) {
      $length = strlen($argument->getName()) + 2;
      if($max < $length) $max = $length;
    }
    foreach($optionSet->getOptions() as $option) {
      $length = strlen($option->getName()) + 6;
      if($max < $length) $max = $length;
    }

    $synopsys = $this->getName();
    if ($name != '')
      $synopsys .= ' '. $name;
    $synopsys .= $argumentSet . $optionSet;

    $res = $this->formatSynopsys($synopsys);
    $res .= $this->formatArguments($argumentSet, $max);
    $res .= $this->formatOptions($optionSet, $max - 6);
    $res .= $this->formatDescription($description);

    return $res;
  }

  protected abstract function formatSynopsys($synopsys);
  protected abstract function formatArguments(nbArgumentSet $argumentSet, $max);
  protected abstract function formatOptions(nbOptionSet $optionSet, $max);
  protected abstract function formatDescription($description);

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

    if(isset($options['help'])) {
      $logger->log($this->formatHelpString('', $this->arguments, $this->options, ''));
      return true;
    }

    return false;
  }

  protected function log($text, $level = null)
  {
    $this->logger->log($text, $level);
  }

  protected function format($text, $level)
  {
    return $this->logger->format($text, $level);
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
   * Registers an array of command objects.
   *
   * If you pass null, this method will register all available commands.
   *
   * @param array  $commands  An array of commands
   */
  public function registerCommands(array $commands = null)
  {
    if (null === $commands)
      $commands = $this->autodiscoverCommands();

    $this->setCommands(new nbCommandSet($commands));
  }


  /**
   * Autodiscovers command classes.
   *
   * @return array An array of command instances
   */
  public function autodiscoverCommands()
  {
    $commands = array();
    foreach (get_declared_classes() as $class) {
      $r = new ReflectionClass($class);

      if($r->isSubclassOf('nbCommand') && !$r->isAbstract()) {
        $commands[] = new $class();
      }
    }

    return $commands;
  }

  public function loadCommands()
  {
    // Symfony core tasks
    //$dirs = array(sfConfig::get('sf_symfony_lib_dir').'/commmand');
    $dirs = array(dirname(__FILE__) . '/../../command');
    //$dirs[] = sfConfig::get('sf_lib_dir').'/command';

    $finder = nbFileFinder::create('file')->add('*Command.php');
    foreach ($finder->in($dirs) as $file)
      $this->commandFiles[basename($file, '.php')] = $file;

    // register local autoloader for tasks
    spl_autoload_register(array($this, 'autoloadCommand'));

    // require tasks
    foreach ($this->commandFiles as $command => $file) {
      // forces autoloading of each task class
      class_exists($command, true);
    }

    // unregister local autoloader
    spl_autoload_unregister(array($this, 'autoloadCommand'));
  }


  /**
   * Autoloads a command class
   *
   * @param  string  $class  The command class name
   *
   * @return Boolean true if the command exists
   */
  public function autoloadCommand($class)
  {
    if (isset($this->commandFiles[$class])) {
      require_once $this->commandFiles[$class];

      return true;
    }

    return false;
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
      $logger->log($message, nbLogger::ERROR);
      $logger->log("\n");
    }

/*    if (null !== $this->currentTask && $e instanceof sfCommandArgumentsException) {
      fwrite(STDERR, $this->formatter->format(sprintf($this->currentTask->getSynopsis(), $this->getName()), 'INFO', STDERR)."\n");
      fwrite(STDERR, "\n");
    }*/

    if ($this->trace) {
      $logger->log("\n");
      $logger->log("Exception trace:\n", nbLogger::COMMENT);

      // exception related properties
      $trace = $e->getTrace();
      $file = $e->getFile() != null ? $e->getFile() : 'n/a';
      $line = $e->getLine() != null ? $e->getLine() : 'n/a';

      $logger->log(sprintf(" %s:%s\n", $file, $line), nbLogger::INFO);

      for ($i = 0, $count = count($trace); $i < $count; ++$i) {
        $class = isset($trace[$i]['class']) ? $trace[$i]['class'] : '';
        $type = isset($trace[$i]['type']) ? $trace[$i]['type'] : '';
        $function = $trace[$i]['function'] . '()';
        $file = isset($trace[$i]['file']) ? $trace[$i]['file'] : 'n/a';
        $line = isset($trace[$i]['line']) ? $trace[$i]['line'] : 'n/a';

        $message = sprintf(" %s%s%s at %s:%s\n", $class, $type, $function, $file, $line);
        $logger->log($message, nbLogger::INFO);
      }
    }
  }
}

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
    $verbose = false,
    $trace = false,
    $serviceContainer,
    $logger = null,
    $parser = null;

  public function __construct(sfServiceContainerBuilder $serviceContainer)
  {
    $this->logger = nbLogger::getInstance();
    $this->arguments = new nbArgumentSet();
    $this->options = new nbOptionSet();
    $this->serviceContainer = $serviceContainer;
    $this->arguments->addArgument(
      new nbArgument('command', nbArgument::REQUIRED, 'The command to execute')
    );

    $this->options->addOptions(array(
      new nbOption('version',     'V', nbOption::PARAMETER_NONE, 'Shows the version'),
      new nbOption('verbose',     'v', nbOption::PARAMETER_NONE, 'Sets verbosity'),
      new nbOption('trace',       '',  nbOption::PARAMETER_NONE, 'Shows exception trace'),
      new nbOption('help',        '?', nbOption::PARAMETER_NONE, 'Shows application help'),
    ));

    $this->configure();
  }

  public function run($commandLine = null)
  {
    $this->parser = new nbCommandLineParser($this->arguments->getArguments(), $this->options->getOptions());
    $this->parser->parse($commandLine);

    if($this->handleOptions($this->parser->getOptionValues()))
      return;

    $this->verifyOptionCommand();

    $commandName = 'list';
    if($this->parser->hasArgumentValue('command'))
      $commandName = $this->parser->getArgumentValue('command');
    else
      $commandLine = $commandName . ' ' . $commandLine;

    if(!$this->getCommands()->hasCommand($commandName))
      return;

    $command = $this->getCommands()->getCommand($commandName);
    $r = new ReflectionClass($command);
    if($r->isSubclassOf('nbApplicationCommand'))
      $command->setApplication($this);

    try {
      $command->run($this->parser, $commandLine, $this->verbose);
    }
    catch(Exception $e) {
      $this->logger->logLine('');
      $helpCmd = new nbHelpCommand();
      $this->logger->log($helpCmd->formatHelp($command));
      $this->logger->logLine('');
      $this->logger->logLine($e->getMessage(), nbLogger::ERROR);
    }
    catch(Exception $e) {
      $this->logger->logLine('');
      $helpCmd = new nbHelpCommand();
      $this->logger->log($helpCmd->formatHelp($command));
      $this->logger->logLine('');
      $this->logger->logLine($e->getMessage(), nbLogger::ERROR);
    }
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

    if(isset($options['help'])) {
      $logger->log($this->formatHelp($this->arguments, $this->options));
      return true;
    }

    if(isset($options['config'])) {
      foreach ($options['config'] as $option) {
        $property = preg_split('/[\s]*=[\s]*/', $option, 2);
        nbConfig::set($property[0], $property[1]);
      }
    }

    if(isset($options['enable-plugin'])) {
      $this->serviceContainer->pluginLoader->loadPlugins($options['enable-plugin']);
    }

    if(isset($options['enable-all-plugins'])) {
      $this->serviceContainer->pluginLoader->loadAllPlugins();
    }

    return false;
  }

  public function formatHelp()
  {
    $max = 0;
    foreach($this->arguments->getArguments() as $argument) {
      $length = strlen($argument->getName()) + 2;
      if($max < $length) $max = $length;
    }
    foreach($this->options->getOptions() as $option) {
      $length = strlen($option->getName()) + 6;
      if($max < $length) $max = $length;
    }

    $synopsys = $this->getName();
    $synopsys .= $this->arguments . $this->options;

    $res = nbHelpFormatter::formatSynopsys($synopsys);
    $res .= nbHelpFormatter::formatArguments($this->arguments, $max);
    $res .= nbHelpFormatter::formatOptions($this->options, $max - 6);

    return $res;
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

  public function hasCommands()
  {
    return $this->getCommands()->count() > 0;
  }

  public function getCommands()
  {
    return $this->serviceContainer->commandLoader->getCommands();
  }

  public function getCommand($name)
  {
    return $this->getCommands()->getCommand($name);
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
    $logger = nbLogger::getInstance();
    $logger->setOutput($output);
    $logger->log("\n");
    foreach ($messages as $message) {
      $logger->log($message, nbLogger::ERROR);
      $logger->log("\n");
    }

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

  public function verifyOptionCommand()
  {
    if(!$this->parser->hasArgumentValue('command'))
      return;
    
    $argument = $this->parser->getArgumentValue('command');

    $currentCommand = $this->getCommand($argument);

    if(null == $currentCommand)
      return;
    
    foreach($currentCommand->getOptionsArray() as $cmdOption) {
      if($this->options->hasOption($cmdOption->getName()))
        throw new Exception(sprintf('[nbApplication::VerifyOption] The "%s" option name already exists in beeApplication.', $cmdOption->getName()));
    }
  }
}

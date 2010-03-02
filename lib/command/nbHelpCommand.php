<?php

/**
 * Prints command help.
 *
 * @package    bee
 * @subpackage command
 */
class nbHelpCommand extends nbCommand
{
  private $application = null;

  public function  __construct(nbApplication $application)
  {
    parent::__construct();
    $this->application = $application;
  }

  protected function configure()
  {
    $this->setName('help')
      ->setBriefDescription('Displays help for a command')
      ->setDescription(<<<TXT
The <info>help</info> command displays help for a given task:

   <info>./bee help</info>
TXT
        );
    
    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('command_name', nbArgument::OPTIONAL, 'The command name'),
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $command = $this;
    if(isset($arguments['command_name'])) {
      $commandName = $arguments['command_name'];
      $command = $this->application->getCommands()->getCommand($commandName);
    }

    $max = 0;
    foreach($command->getArgumentsArray() as $argument) {
      $length = strlen($argument->getName()) + 2;
      if($max < $length) $max = $length;
    }
    foreach($command->getOptionsArray() as $option) {
      $length = strlen($option->getName()) + 2;
      if($max < $length) $max = $length;
    }

    $res = $this->printSynopsys($command);

    $res .= $this->printArguments($command, $max);
    $res .= $this->printOptions($command, $max);
    $res .= $this->printDescription($command);

    $this->log($res);
  }

  public function printSynopsys($command)
  {
    $res = $this->format("Usage:", 'comment') . "\n";
    $res .= ' ' . $this->format($command->getSynopsys(), 'info') . "\n";
    
    return $res;
  }

  public function printArguments($command, $max)
  {
    $arguments = $command->getArgumentsArray();
    if(count($arguments) == 0)
      return '';

    $res = "\n";
    $res .= $this->format("Arguments:", 'comment') . "\n";

    foreach($arguments as $argument) {
      $res .= $this->format(sprintf(" %-{$max}s ", $argument->getName()), 'info');
      $res .= $argument->getDescription();
      if($argument->isRequired())
        $res .= $this->format(' (required)', 'comment');
      else if(null !== $argument->getValue() && !$argument->isArray())
        $res .= $this->format(' (default: ' . $argument->getValue() . ')', 'comment');
      $res .= "\n";
    }
    return $res;
  }

  public function printOptions($command, $max)
  {
    $options = $command->getOptionsArray();
    if(count($options) == 0)
      return '';

    $res = "\n";
    $res .= $this->format("Options:", 'comment') . "\n";
    foreach($options as $option) {
      $res .= $this->format(sprintf(" %-{$max}s %s", $option->getName(), $option->getShortcut()), 'info');
      $res .= ' ' . $option->getDescription();
      if($option->hasOptionalParameter() && !$option->isArray())
        $res .= $this->format(' (default: ' . $option->getValue() . ')', 'comment');
      $res .= "\n";
    }
    return $res;
  }

  public function printDescription($command, $indent = ' ')
  {
    $detailedDescription = $command->getDescription();
    if(!$detailedDescription)
      return '';

    $res = "\n";
    $res .= $this->format('Description:', 'comment') . "\n";

    $res .= ' ' . implode("\n ", explode("\n", $detailedDescription)) . "\n";

    return $res;
  }
}

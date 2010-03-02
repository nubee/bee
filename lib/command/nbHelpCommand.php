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
      ->setBriefDescription('print command help')
      ->setDescription(<<<TXT
The <info>help</info> command displays help for a given task:
   ./bee help test:all
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

    nbLogger::getInstance()->log($this->printSynopsys($command));
    return true;
  }

  public function printSynopsys($command)
  {
    $res = nbLogger::getInstance()->format("Usage:", 'comment') . "\n";
    $res .= ' ' . $command->getSynopsys() . "\n";
    $res .= $this->printArguments($command, 0);
    $res .= $this->printOptions($command, 0);
    $res .= $this->printDescription($command);
    return $res;
  }

  public function printArguments($command, $max)
  {
    $argumentSet = $command->getArguments();
    if(count($argumentSet->getArguments()) == 0)
      return '';

    $res = "\n" . nbLogger::getInstance()->format("Arguments:", 'comment') . "\n";
    foreach($argumentSet->getArguments() as $argument) {
      $res .= sprintf(' %-' . ($max + 7) . 's', nbLogger::getInstance()->format($argument->getName(), 'info'));
      $res .= ' ' . $argument->getDescription();
      if(!$argument->isRequired())
        $res .= nbLogger::getInstance()->format(' (default: ' . $argument->getValue() . ')', 'comment');
      $res .= "\n";
    }
    return $res;
  }

  public function printOptions($command, $max)
  {
    $optionSet = $command->getOptions();
    if(count($optionSet->getOptions()) == 0)
      return '';

    $res = "\n" . nbLogger::getInstance()->format("Options:", 'comment') . "\n";
    foreach($optionSet->getOptions() as $option) {
      $res .= sprintf(' %-' . ($max + 7) . 's', nbLogger::getInstance()->format($option->getName(), 'info'));
      $res .= ' ' . $option->getDescription();
      if($option->hasOptionalParameter())
        $res .= nbLogger::getInstance()->format(' (default: ' . $option->getValue() . ')', 'comment');
      $res .= "\n";
    }
    return $res;
  }

  public function printDescription($command, $indent = ' ')
  {
    if($command->getDescription() == '')
      return '';

    $res = "\n" . nbLogger::getInstance()->format("Description:", 'comment') . "\n";
    $lines = preg_split("/[\n\r]/", $command->getDescription());
    foreach($lines as $line)
      $res .= $indent . $line . "\n";
    return $res;
  }
}

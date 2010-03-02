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

    $this->log($this->printSynopsys($command));
    return true;
  }

  public function printSynopsys($command)
  {
    $res = $this->format("Usage:", 'comment') . "\n";
    $res .= ' ' . $this->format($command->getSynopsys(), 'info') . "\n";
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

    $res = "\n";
    $res .= $this->format("Arguments:", 'comment') . "\n";

    foreach($argumentSet->getArguments() as $argument) {
      $res .= sprintf(' %-' . ($max + 7) . 's ', $this->format($argument->getName(), 'info'));
      $res .= $argument->getDescription();
      if($argument->isRequired())
        $res .= $this->format(' (required)', 'comment');
      else if(null !== $argument->getValue())
        $res .= $this->format(' (default: ' . $argument->getValue() . ')', 'comment');
      $res .= "\n";
    }
    return $res;
  }

  public function printOptions($command, $max)
  {
    $optionSet = $command->getOptions();
    if(count($optionSet->getOptions()) == 0)
      return '';

    $res = "\n\n";
    $this->format("Options:", 'comment') . "\n";
    foreach($optionSet->getOptions() as $option) {
      $res .= sprintf(' %-' . ($max) . 's', $this->format($option->getName(), 'info'));
      $res .= $option->hasShortcut() ? sprintf('%5s', $option->getShortcut()) :
      $res .= ' ' . $option->getDescription();
      if($option->hasOptionalParameter())
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

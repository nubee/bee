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
  private $output = null;

  public function  __construct(nbApplication $application)
  {
    parent::__construct();
    $this->application = $application;
    $this->output = new nbConsoleOutput();
  }

  public function setOutput(nbOutput $output)
  {
    $this->output = $output;
  }

  protected function configure()
  {
    $this->setName('help')
      ->setBriefDescription('print command help')
      ->setDescription('')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('commandName', nbArgument::OPTIONAL, 'The command name'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    if(count($arguments['commandName'])) {
      $commandName = $arguments['commandName'];
      $command = $this->application->getCommands()->getCommand($commandName);
      $this->output->write($command->getName());
    }
    else {
      $this->output->write('help');
    }
    return true;
  }
}

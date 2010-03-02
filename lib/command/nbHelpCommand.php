<?php

/**
 * Prints command help.
 *
 * @package    bee
 * @subpackage command
 */
class nbHelpCommand extends nbCommand
{
  private $output;// = new nbConsoleOutput();

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
        new nbArgument('command', nbArgument::OPTIONAL, 'The command name'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    if(count($arguments['commands'])) {
      $cmd = $arguments['commands'];
      $this->output->write($cmd);
    }
    else
    {
      $this->output->write('help');
    }
    return true;
  }
}

<?php

/**
 * Generates code documentation.
 *
 * @package    bee
 * @subpackage command
 */
class nbDoxygenGenerateCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('doxygen:generate')
      ->setBriefDescription('Generates code documentation')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command generates code documentation:

   <info>./bee {$this->getFullName()} repository local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $antClient = new nbAntClient();
    $command = "ant docs";
    $antClient->getCommandLine($command);
    $shell = new nbShell();
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbDoxigenGenerateCommand::execute] Error executing command:
  %s", $command));
    }
  }
}


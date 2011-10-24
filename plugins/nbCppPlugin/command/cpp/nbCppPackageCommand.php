<?php

/**
 * Generates package.
 *
 * @package    bee
 * @subpackage command
 */
class nbCppPackageCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('cpp:package')
      ->setBriefDescription('Generates package')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command generates code documentation:

   <info>./bee {$this->getFullName()} repository local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $antClient = new nbAntClient();
    $command = "ant archive";
    
    $antClient->getCommandLine($command);
    
    $this->executeShellCommand($command);
  }
}

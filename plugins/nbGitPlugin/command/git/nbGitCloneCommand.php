<?php

/**
 * Clones a git repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbGitCloneCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('git:clone')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('repository', nbArgument::REQUIRED, 'The repository to clone'),
        new nbArgument('local', nbArgument::OPTIONAL, 'The path into clone repository', '.')
      )))
      ->setBriefDescription('Clones a git repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command clones a git repository:

   <info>./bee {$this->getFullName()} repository local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $repository = $arguments['repository'];
    $localRepository = $arguments['local'];
    
    $this->logLine(sprintf('Cloning repository %s in %s', $repository, $local));
    
    $command = sprintf('git clone "%s" "%s"', $repository, $local);

    $this->executeShellCommand($command);
  }
}

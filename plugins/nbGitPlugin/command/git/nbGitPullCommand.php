<?php

/**
 * Pulls from a git repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbGitPullCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('git:pull')
      ->addArgument(new nbArgument('repository', nbArgument::OPTIONAL, 'The repository to pull from', 'origin'))
      ->addArgument(new nbArgument('branch', nbArgument::OPTIONAL, 'The branch to pull to', 'master'))
      ->setBriefDescription('Pulls from a git repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command pulls from a git repository:

   <info>./bee {$this->getFullName()} origin master</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $repository = $arguments['repository'];
    $branch = $arguments['branch'];
    $this->logLine(sprintf('Pulling from repository "%s" to "%s"', $repository, $branch));

    $command = sprintf('git pull "%s" "%s"', $repository, $branch);
    $this->executeShellCommand($command);
  }
}

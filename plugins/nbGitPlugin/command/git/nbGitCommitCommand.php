<?php

/**
 * Commit changes into local git repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbGitCommitCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('git:commit')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('message', nbArgument::REQUIRED, 'The commit message')
      )))
      ->setBriefDescription('Commits changes into local git working copy')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command commit changes into local repository:

   <info>./bee {$this->getFullName()} message</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Commiting git changes');
    
    $message = $arguments['message'];

    $command = 'git add .';
    $this->executeShellCommand($command);

    $command = sprintf('git commit -a -m "%s"', $message);
    $this->executeShellCommand($command);

    $this->logLine('Git changes committed successfully');
  }
}

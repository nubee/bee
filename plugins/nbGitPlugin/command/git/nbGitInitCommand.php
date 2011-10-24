<?php

/**
 * Initializes a git repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbGitInitCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('git:init')
      ->setBriefDescription('Initializes a git repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command initializes a git repository:

   <info>./bee {$this->getFullName()}</info>
TXT
        );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Initializing git repository', nbLogger::COMMENT);

    $this->executeShellCommand('git init');
    
    $this->logLine(sprintf('git repository initialized successfully in %s', getcwd()), nbLogger::COMMENT);
  }
}

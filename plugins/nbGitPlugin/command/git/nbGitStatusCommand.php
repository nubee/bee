<?php

/**
 * Shows local repository status.
 *
 * @package    bee
 * @subpackage command
 */
class nbGitStatusCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('git:status')
      ->setBriefDescription('Shows local repository status')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command shows local repository status:

    <info>./bee {$this->getFullName()}</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Status of branch', nbLogger::COMMENT);

    $this->executeShellCommand('git status');
  }
}

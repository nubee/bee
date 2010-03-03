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
The <info>git:init</info> command initializes a git repository:

   <info>./bee git:init</info>
TXT
        );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
  }
}

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
      ->setBriefDescription('Clones a git repository')
      ->setDescription(<<<TXT
The <info>git:clone</info> command clones a git repository:

   <info>./bee git:clone</info>
TXT
        );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
  }
}

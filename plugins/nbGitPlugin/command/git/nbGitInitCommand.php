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
    $this->log('Initializing repository', nbLogger::COMMENT);
    $this->log("\n");
    $shell = new nbShell();

    if(!$shell->execute('git init')) {
      throw new LogicException("[nbGitInitCommand::execute] Error executing command");
    }
    
    //$this->log($this->formatLine(' ' . implode("\n ", $shell->getOutput()), nbLogger::COMMENT));
  }
}

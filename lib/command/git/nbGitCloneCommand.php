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
        new nbArgument('repository', nbArgument::REQUIRED, 'The repository to clone')
      )))
      ->setBriefDescription('Clones a git repository')
      ->setDescription(<<<TXT
The <info>git:clone</info> command clones a git repository:

   <info>./bee git:clone repository</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Cloning git repository: ' . $arguments['repository'], nbLogger::COMMENT);
    $this->log("\n");
    $shell = new nbShell(true);
    $shell->execute('git clone ' . $arguments['repository']);

    //$this->log($this->formatLine(' ' . implode("\n ", $shell->getOutput()), nbLogger::COMMENT));
  }
}

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
    $this->log('Pulling from repository ', nbLogger::COMMENT);
    $this->log($arguments['repository']);
    $this->log(' to ', nbLogger::COMMENT);
    $this->log($arguments['branch']);
    $this->log("\n");
    $shell = new nbShell();

    $command = 'git pull "' . $arguments['repository'] . '" "' . $arguments['branch'] . '"';
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbGitPullCommand::execute] Error executing command:
  %s
  repository -> %s
  branch     -> %s",
        $command, $arguments['repository'], $arguments['branch']
      ));
    }

    //$this->log($this->formatLine(' ' . implode("\n ", $shell->getOutput()), nbLogger::COMMENT));
  }
}

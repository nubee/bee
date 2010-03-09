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
The <info>git:pull</info> command pulls from a git repository:

   <info>./bee git:pull origin master</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Pulling from repository: ' . $arguments['repository'], nbLogger::COMMENT);
    $this->log('  --> to: ' . $arguments['branch'], nbLogger::COMMENT);
    $this->log("\n");
    $shell = new nbShell();

    if(!$shell->execute(sprintf('git pull %s %s', $arguments['repository'], $arguments['branch']))) {
      throw new LogicException(sprintf(
        "[nbGitPullCommand::execute] Error executing command:\n  repository arg -> %s\n  branch arg -> %s",
        $arguments['repository'], $arguments['branch']
      ));
    }

    //$this->log($this->formatLine(' ' . implode("\n ", $shell->getOutput()), nbLogger::COMMENT));
  }
}

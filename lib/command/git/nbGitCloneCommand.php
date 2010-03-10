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
        new nbArgument('repository', nbArgument::REQUIRED, 'The repository to clone'),
        new nbArgument('local', nbArgument::OPTIONAL, 'The path into clone repository', '.')
      )))
      ->setBriefDescription('Clones a git repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command clones a git repository:

   <info>./bee {$this->getFullName()} repository local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Cloning repository ', nbLogger::COMMENT);
    $this->log($arguments['repository']);
    $this->log(' in ', nbLogger::COMMENT);
    $this->log($arguments['repository']);
    $this->log("\n");
    $shell = new nbShell();
    $command = 'git clone "' . $arguments['repository'] . '" "' . $arguments['local'] . '"';

    //TODO: $shell->execute($command) returns true on git error (!)
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbGitCloneCommand::execute] Error executing command:
  %s
  repository -> %s
  local      -> %s
",
        $command, $arguments['repository'], $arguments['local']
      ));
    }

    //$this->log($this->formatLine(' ' . implode("\n ", $shell->getOutput()), nbLogger::COMMENT));
  }
}

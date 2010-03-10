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
      ->setBriefDescription('Commit changes')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command commit changes into local repository:

   <info>./bee {$this->getFullName()} message</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Commit changes', nbLogger::COMMENT);
    $this->log("\n");
    $shell = new nbShell();

    $command = 'git add .';
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbGitCommitCommand::execute] Error executing command:
  %s
",
        $command
      ));
    }

    $command = 'git commit -a -m "' . $arguments['message'] . '"';
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbGitCommitCommand::execute] Error executing command:
  %s
",
        $command
      ));
    }
  }
}

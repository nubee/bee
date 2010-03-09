<?php

/**
 * Commit changes to the svn repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbSvnCommitCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('svn:commit')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('message', nbArgument::REQUIRED, 'Commit message'),
        new nbArgument('local', nbArgument::OPTIONAL, 'Working copy path', '.')
      )))
      ->setBriefDescription('Send changes from your working copy to the repository.')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command send changes from your working copy to the repository:

    <info>./bee {$this->getFullName()} message local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Committing changes of ' . $arguments['local'], nbLogger::COMMENT);
    $this->log("\n");
    $shell = new nbShell();
    if(!$shell->execute('svn commit ' . $arguments['local'] . ' -m "' . $arguments['message'] . '"')) {
      throw new LogicException(sprintf(
        "[nbSvnCommitCommand::execute] Error executing command:\n  message arg -> %s\n  local arg -> %s",
        $arguments['message'], $arguments['local']
      ));
    }
  }
}

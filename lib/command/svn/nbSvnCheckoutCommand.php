<?php

/**
 * Checkout from a svn repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbSvnCheckoutCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('svn:checkout')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('repository', nbArgument::REQUIRED, 'Repository path'),
        new nbArgument('local', nbArgument::OPTIONAL, 'Working copy path', '.')
      )))
      ->setBriefDescription('Check out a working copy from a repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command check out a working copy from a repository:

    <info>./bee {$this->getFullName()} repository local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Checking out ' . $arguments['repository'] . ' in ' . $arguments['local'], nbLogger::COMMENT);
    $this->log("\n");
    $shell = new nbShell();
    if(!$shell->execute('svn checkout ' . $arguments['repository'] . ' ' . $arguments['local'])) {
      throw new LogicException(sprintf(
        "[nbSvnCheckoutCommand::execute] Error executing command:\n  repository arg -> %s\n  local arg -> %s",
        $arguments['repository'], $arguments['local']
      ));
    }
  }
}

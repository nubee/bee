<?php

/**
 * Shows working copy status.
 *
 * @package    bee
 * @subpackage command
 */
class nbSvnStatusCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('svn:status')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('local', nbArgument::OPTIONAL, 'Working copy path', '.')
      )))
      ->setBriefDescription('Shows working copy status')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command shows working copy stauts:

    <info>./bee {$this->getFullName()} local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Status of ' . $arguments['local'], nbLogger::COMMENT);
    $this->log("\n");
    $shell = new nbShell();
    if(!$shell->execute('svn status ' . $arguments['local'])) {
      throw new LogicException(sprintf(
        "[nbSvnStatusCommand::execute] Error executing command:\n  local arg -> %s",
        $arguments['local']
      ));
    }
  }
}

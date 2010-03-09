<?php

/**
 * Update a working copy.
 *
 * @package    bee
 * @subpackage command
 */
class nbSvnUpdateCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('svn:update')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('local', nbArgument::OPTIONAL, 'Working copy path', '.')
      )))
      ->setBriefDescription('Update a working copy')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command update a working copy:

    <info>./bee {$this->getFullName()} local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Updating ' . $arguments['local'], nbLogger::COMMENT);
    $this->log("\n");
    $shell = new nbShell();
    if(!$shell->execute('svn update ' . $arguments['local'])) {
      throw new LogicException(sprintf(
        "[nbSvnUpdateCommand::execute] Error executing command:\n  local arg -> %s",
        $arguments['local']
      ));
    }
  }
}

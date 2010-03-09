<?php

/**
 * Commit unversioned files or folders into the svn repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbSvnImportCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('svn:import')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('message', nbArgument::REQUIRED, 'Import message'),
        new nbArgument('repository', nbArgument::REQUIRED, 'Repository path'),
        new nbArgument('local', nbArgument::OPTIONAL, 'Working copy path', '.')
      )))
      ->setBriefDescription('Commit unversioned files or folders into the svn repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command commit unversioned files or folders into the svn repository:

    <info>./bee {$this->getFullName()} message repository local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Importing ' . $arguments['local'] . ' in ' . $arguments['repository'], nbLogger::COMMENT);
    $this->log("\n");
    $shell = new nbShell();
    if(!$shell->execute('svn import ' . $arguments['local'] . ' ' . $arguments['repository'] . ' -m "' . $arguments['message'] . '"')) {
      throw new LogicException(sprintf(
        "[nbSvnImportCommand::execute] Error executing command:\n  message arg -> %s\n  repository arg -> %s\n  local arg -> %s",
        $arguments['message'], $arguments['repository'], $arguments['local']
      ));
    }
  }
}

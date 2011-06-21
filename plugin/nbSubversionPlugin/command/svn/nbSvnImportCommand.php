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
      ->setOptions(new nbOptionSet(array(
        new nbOption('username', 'u', nbOption::PARAMETER_REQUIRED, 'Specify an username'),
        new nbOption('password', 'p', nbOption::PARAMETER_REQUIRED, 'Specify a password')
      )))
      ->setBriefDescription('Imports unversioned files or folders into the svn repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command commit unversioned files or folders into the svn repository:

    <info>./bee {$this->getFullName()} message repository local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Importing ', nbLogger::COMMENT);
    $this->log($arguments['local']);
    $this->log(' in ', nbLogger::COMMENT);
    $this->log($arguments['repository']);
    $this->log("\n");
    $shell = new nbShell();
    $client = new nbSvnClient();

    $command = $client->getImportCmdLine(
      $arguments['local'],
      $arguments['repository'],
      $arguments['message'],
      isset($options['username']) ? $options['username'] : '',
      isset($options['password']) ? $options['password'] : ''
    );

    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbSvnImportCommand::execute] Error executing command:
  %s
  local      -> %s
  repository -> %s
  message    -> %s
  username   -> %s
  password   -> %s
",
        $command, $arguments['local'], $arguments['repository'], $arguments['message'], $options['username'], $options['password']
      ));
    }
  }
}

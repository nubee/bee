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
  local      -> %s
  repository -> %s
  message    -> %s
  username   -> %s
  password   -> %s
",
        $arguments['local'], $arguments['repository'], $arguments['message'], $options['username'], $options['password']
      ));
    }
  }
}

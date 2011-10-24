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
      ->setBriefDescription('Imports unversioned files or folders into the svn repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command commit unversioned files or folders into the svn repository:

    <info>./bee {$this->getFullName()} message repository local</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('message', nbArgument::REQUIRED, 'Import message'),
        new nbArgument('repository', nbArgument::REQUIRED, 'Repository path'),
        new nbArgument('local', nbArgument::OPTIONAL, 'Working copy path', '.')
      )));
    
    $this->setOptions(new nbOptionSet(array(
        new nbOption('username', 'u', nbOption::PARAMETER_REQUIRED, 'Specify an username'),
        new nbOption('password', 'p', nbOption::PARAMETER_REQUIRED, 'Specify a password')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $local = $arguments['local'];
    $repository = $arguments['repository'];
    $message = $arguments['message'];
    $username = isset($options['username']) ? $options['username'] : '';
    $password = isset($options['password']) ? $options['password'] : '';

    $this->logLine(sprintf('Committing repository "%s" in "%s"', $local, $repository), nbLogger::COMMENT);

    $client = new nbSvnClient();

    $command = $client->getImportCmdLine($local, $repository, $message, $username, $password);

    $this->executeShellCommand($command);
  }

}

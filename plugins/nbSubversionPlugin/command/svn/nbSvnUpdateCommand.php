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
      ->setOptions(new nbOptionSet(array(
        new nbOption('username', 'u', nbOption::PARAMETER_REQUIRED, 'Specify an username'),
        new nbOption('password', 'p', nbOption::PARAMETER_REQUIRED, 'Specify a password')
      )))
      ->setBriefDescription('Updates a working copy')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command update a working copy:

    <info>./bee {$this->getFullName()} local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $local = $arguments['local'];
    $username = isset($options['username']) ? $options['username'] : '';
    $password = isset($options['password']) ? $options['password'] : '';
    
    $this->logLine(sprintf('Updating repository: %s', $local), nbLogger::COMMENT);

    $client = new nbSvnClient();

    $command = $client->getUpdateCmdLine($local, $username, $password);

    $this->executeShellCommand($command);
  }
}

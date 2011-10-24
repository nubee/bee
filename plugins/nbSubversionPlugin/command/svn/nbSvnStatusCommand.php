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
      ->setBriefDescription('Shows working copy status')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command shows working copy stauts:

    <info>./bee {$this->getFullName()} local</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
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
    $username = isset($options['username']) ? $options['username'] : '';
    $password = isset($options['password']) ? $options['password'] : '';

    $this->logLine(sprintf('SVN status of repository: %s', $local), nbLogger::COMMENT);

    $client = new nbSvnClient();

    $command = $client->getStatusCmdLine($local, $username, $password);

    $this->executeShellCommand($command);
  }

}

<?php

/**
 * Check out from a svn repository.
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
      ->setOptions(new nbOptionSet(array(
        new nbOption('username', 'u', nbOption::PARAMETER_REQUIRED, 'Specify an username'),
        new nbOption('password', 'p', nbOption::PARAMETER_REQUIRED, 'Specify a password')
      )))
      ->setBriefDescription('Checks out a working copy from a repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command check out a working copy from a repository:

    <info>./bee {$this->getFullName()} repository local</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Checking out ', nbLogger::COMMENT);
    $this->log($arguments['repository']);
    $this->log(' in ', nbLogger::COMMENT);
    $this->log($arguments['local']);
    $this->log("\n");
    $shell = new nbShell();
    $client = new nbSvnClient();

    $command = $client->getCheckoutCmdLine(
      $arguments['repository'],
      $arguments['local'],
      true, //force checkout
      isset($options['username']) ? $options['username'] : '',
      isset($options['password']) ? $options['password'] : ''
    );
    
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbSvnCheckoutCommand::execute] Error executing command:
  %s
  repository -> %s
  local      -> %s
  username   -> %s
  password   -> %s
",
        $command, $arguments['repository'], $arguments['local'], $options['username'], $options['password']
      ));
    }
  }
}

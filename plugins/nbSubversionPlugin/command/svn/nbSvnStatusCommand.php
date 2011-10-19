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
      ->setOptions(new nbOptionSet(array(
        new nbOption('username', 'u', nbOption::PARAMETER_REQUIRED, 'Specify an username'),
        new nbOption('password', 'p', nbOption::PARAMETER_REQUIRED, 'Specify a password')
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
    $this->log('Status of ', nbLogger::COMMENT);
    $this->log($arguments['local']);
    $this->log("\n");
    $shell = new nbShell();
    $client = new nbSvnClient();

    $command = $client->getStatusCmdLine(
      $arguments['local'],
      isset($options['username']) ? $options['username'] : '',
      isset($options['password']) ? $options['password'] : ''
    );

    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbSvnStatusCommand::execute] Error executing command:
  %s
  local    -> %s
  username -> %s
  password -> %s
",
        $command, $arguments['local'], $options['username'], $options['password']
      ));
    }
  }
}

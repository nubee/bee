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
    $this->log('Updating ', nbLogger::COMMENT);
    $this->log($arguments['local']);
    $this->log("\n");
    $shell = new nbShell();
    $client = new nbSvnClient();

    $command = $client->getUpdateCmdLine(
      $arguments['local'],
      isset($options['username']) ? $options['username'] : '',
      isset($options['password']) ? $options['password'] : ''
    );

    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbSvnUpdateCommand::execute] Error executing command:
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

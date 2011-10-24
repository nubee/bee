<?php

/**
 * Commit changes to the svn repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbSvnCommitCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('svn:commit')
      ->setBriefDescription('Commits changes from your working copy to the repository.')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command send changes from your working copy to the repository:

    <info>./bee {$this->getFullName()} message local</info>
TXT
    );
    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('message', nbArgument::REQUIRED, 'Commit message'),
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
    $message = $arguments['message'];
    $username = isset($options['username']) ? $options['username'] : '';
    $password = isset($options['password']) ? $options['password'] : '';
    
    $this->logLine(sprintf('Committing changes in repository "%s"', $local), nbLogger::COMMENT);

    if(file_exists($local . '/version.yml')) {
      $cmd = new nbUpdateBuildVersionCommand();
      $cmd->run(new nbCommandLineParser(), $local . '/version.yml');
    }

    $client = new nbSvnClient();

    $command = $client->getCommitCmdLine($local, $message, $username, $password );

    $this->executeShellCommand($command);
  }

}

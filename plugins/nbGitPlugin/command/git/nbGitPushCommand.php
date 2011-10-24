<?php

/**
 * Push in a git repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbGitPushCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('git:push')
      ->addArgument(new nbArgument('repository', nbArgument::OPTIONAL, 'The repository to push to', 'origin'))
      ->addArgument(new nbArgument('branch', nbArgument::OPTIONAL, 'The branch to push from', 'master'))
      ->setBriefDescription('Pushes to a git repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command push to a git repository:

   <info>./bee {$this->getFullName()} origin master</info>
TXT
    );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $repository = $arguments['repository'];
    $branch = $arguments['branch'];
    $this->logLine(sprintf('Pushing from repository "%s" into "%s"', $branch, $repository));

    $versionYaml = './version.yml';
    if(file_exists($versionYaml)) {
      $cmd = new nbUpdateBuildVersionCommand();
      $cmd->run(new nbCommandLineParser(), $versionYaml);

      $command = 'git add ' . $versionYaml;
      $this->executeShellCommand($command, 1);

      $command = 'git commit -m "build version update"';
      $this->executeShellCommand($command, 1);
    }

    $command = sprintf('git push "%s" "%s"', $repository, $branch);
    $this->executeShellCommand($command);
  }

}

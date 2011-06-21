<?php

/**
 * Push in a git repository.
 *
 * @package    bee
 * @subpackage command
 */
class nbGitPushCommand extends nbCommand {

  protected function configure() {
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

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->log('Pushing ', nbLogger::COMMENT);
    $this->log($arguments['branch']);
    $this->log(' into repository ', nbLogger::COMMENT);
    $this->log($arguments['repository']);
    $this->log("\n");
    $shell = new nbShell();
    $versionYaml = './version.yml';
    if (file_exists($versionYaml)) {
      $cmd = new nbUpdateBuildVesionCommand();
      $cmd->run(new nbCommandLineParser(), $versionYaml);

      $command = 'git add ' . $versionYaml;
      if (!$shell->execute($command)) {
        throw new LogicException(sprintf("
[nbGitCommitCommand::execute] Error executing command:
  %s
", $command
        ));
      }

      $command = 'git commit -m "build version update"';
      if (!$shell->execute($command)) {
        throw new LogicException(sprintf("
[nbGitCommitCommand::execute] Error executing command:
  %s
", $command
        ));
      }
    }
    $command = 'git push "' . $arguments['repository'] . '" "' . $arguments['branch'] . '"';
    if (!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbGitPushCommand::execute] Error executing command:
  %s
  repository -> %s
  branch     -> %s", $command, $arguments['repository'], $arguments['branch']
      ));
    }

    //$this->log($this->formatLine(' ' . implode("\n ", $shell->getOutput()), nbLogger::COMMENT));
  }

}


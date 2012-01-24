<?php

class nbSymfonyTestAllCommand extends nbCommand {

  protected function configure() {
    $this->setName('symfony:test-all')
            ->setBriefDescription('Launch all tests for symfony projects')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
            )));

    $this->setOptions(new nbOptionSet(array(
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    if (nbConfig::has('project_type') and nbConfig::get('project_type') == 'symfony') {
      $symfonyExecutable = sprintf('%s/%s', nbFileSystem::sanitizeDir(nbConfig::get('project_symfony_exec-path')), 'symfony');
      $symfonyTestEnviroment = nbConfig::get('project_symfony_test-enviroment');
      $this->logLine(sprintf('Launching all test for %s enviroment', $symfonyTestEnviroment));
      if ($symfonyTestEnviroment == 'lime') {
        $cmd = sprintf('php %s test:all', $symfonyExecutable);
        $this->executeShellCommand($cmd);
      } else if ($symfonyTestEnviroment == 'phpunit') {
        $cmd = sprintf('php %s phpunit:test-all', $symfonyExecutable);
        $this->executeShellCommand($cmd);
      } else {
        return false;
      }
      return true;
    }
    else
      throw new Exception('This isn\'t a symfony project');
  }

}
<?php

class nbSymfonyDeployCommand extends nbApplicationCommand
{

  protected function configure()
  {
    $this->setName('symfony:project-deploy')
      ->setBriefDescription('Deploys a symfony project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->addArgument(
      new nbArgument('config-file', nbArgument::REQUIRED, 'Configuration file')
    );

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    // bee project must be defined
    if(!is_dir('./.bee') && !file_exists('./bee.yml')) {
      $message = 'No bee project defined!';
      $message .= "\n\n  Run: bee bee:generate-project";

      throw new InvalidArgumentException($message);
    }

    $this->logLine('Running: symfony:project-deploy', nbLogger::COMMENT);

    $config = $arguments['config-file'];
    $doit = isset($options['doit']);
    $verbose = isset($options['verbose']) || !$doit;

    if(!file_exists($config)) {
      $cmd = new nbConfigPluginCommand();
      $this->executeCommand($cmd, 'nbSymfonyPlugin --force', $doit, $verbose);

      $this->logLine('Configuration file "' . $config . '" created.', nbLogger::INFO);
      $this->logLine('Modify it and re-run the command.', nbLogger::INFO);

      return true;
    }

    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($config);

    $symfonyRootDir = nbConfig::get('symfony_project-deploy_symfony-root-dir');

    // Put site offline
    if(nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach(nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOfflineCommand();

        $cmdLine = sprintf('%s %s %s', $symfonyRootDir, nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_name'), nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_env'));

        $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
      }
    }
    // Archive site directory
    if(nbConfig::has('archive_inflate-dir')) {
      $cmd = new nbInflateDirCommand();
      $cmdLine = '--config-file=' . $config;
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    }

    // Sync project
    if(nbConfig::has('filesystem_dir-transfer')) {
      $cmd = new nbDirTransferCommand();
      $cmdLine = '--doit --delete --config-file=' . $config;
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    }

    // Check dirs
    $cmd = new nbSymfonyCheckDirsCommand();
    $cmdLine = nbConfig::get('symfony_project-deploy_symfony-exe-path');
    $this->executeCommand($cmd, $symfonyRootDir, $doit, $verbose);

    // Check permissions
    $cmd = new nbSymfonyCheckPermissionsCommand();
    $cmdLine = nbConfig::get('symfony_project-deploy_symfony-exe-path');
    $this->executeCommand($cmd, $symfonyRootDir, $doit, $verbose);

    // Change ownership
    $cmd = new nbSymfonyChangeOwnershipCommand();
    $cmdLine = sprintf('%s %s %s', $symfonyRootDir, nbConfig::get('symfony_project-deploy_site-user'), nbConfig::get('symfony_project-deploy_site-group'));
    $this->executeCommand($cmd, $cmdLine, $doit, $verbose);

    // Clear cache
    $cmd = new nbSymfonyClearCacheCommand();
    $this->executeCommand($cmd, $symfonyRootDir, $doit, $verbose);

    // Put site online
    if(nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach(nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOnlineCommand();
        $cmdLine = sprintf('%s %s %s', $symfonyRootDir, nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_name'), nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_env'));

        $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
      }
    }
    $this->logLine('Symfony project deployed successfully');

    return true;
  }

  private function executeCommand(nbCommand $command, $commandLine, $doit, $verbose)
  {
    if($doit)
      $command->run(new nbCommandLineParser(), $commandLine);

    if($verbose)
      $this->logLine(sprintf("  <comment>Executing command: %s</comment>\n   %s\n", $command->getFullName(), $commandLine));
  }

}
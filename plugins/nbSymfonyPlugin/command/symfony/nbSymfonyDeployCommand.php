<?php

class nbSymfonyDeployCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony:project-deploy')
            ->setBriefDescription('Deploys a symfony project')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('environment', nbArgument::REQUIRED, 'Environment name ( stage | prod )'),
            )));


    $this->setOptions(new nbOptionSet(array(
                new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Deploying symfony project', nbLogger::COMMENT);

    // bee project must be defined
    if (!is_dir('./.bee') && !file_exists('./bee.yml')) {
      $message = 'No bee project defined!';
      $message .= "\n\n  Run: bee bee:generate-project";

      throw new InvalidArgumentException($message);
    }

    $doit = isset($options['doit']);
    $verbose = isset($options['verbose']) || !$doit;
    
    // Load configuration
    if (!isset($options['config-file']))
      throw new Exception('--config-file option required (CHANGE THIS)');

    $configDir = nbConfig::get('nb_plugins_dir') . '/nbSymfonyPlugin/config/';
    $configFilename = $options['config-file'];

    $this->loadConfiguration($configDir, $configFilename);

    $symfonyRootDir = nbConfig::get('symfony_project-deploy_symfony-root-dir');

    // Put site offline
    if (nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach (nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOfflineCommand();
        $cmdLine = sprintf('%s %s %s', $symfonyRootDir, nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_name'), nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_env'));
        $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
      }
    }

    // Archive site directory
    if (nbConfig::has('archive_archive-dir')) {
      $cmd = new nbArchiveDirCommand();
      $cmdLine = sprintf('--config-file=%s --create-destination-dir', $configFilename);
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    }

    // Sync project
    if (nbConfig::has('filesystem_dir-transfer')) {
      $cmd = new nbDirTransferCommand();
      $cmdLine = sprintf('%s --config-file=%s', $doit?'--doit':'', $configFilename);
      $parser = new nbCommandLineParser();
      $parser->setDefaultConfigurationDirs($this->getParser()->getDefaultConfigurationDirs());
      if (!$cmd->run($parser, $cmdLine))
        throw new Exception('Error executing: ' . $cmd);
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
    $cmd = new nbChangeOwnershipCommand();
    $cmdLine = sprintf('%s %s %s', $symfonyRootDir, nbConfig::get('symfony_project-deploy_site-user'), nbConfig::get('symfony_project-deploy_site-group'));
    try {
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    } catch (Exception $e) {
      $this->logLine('Cannot change permissions', nbLogger::ERROR);
    }

    // Clear cache
    $cmd = new nbSymfonyClearCacheCommand();
    $this->executeCommand($cmd, $symfonyRootDir, $doit, $verbose);

    // Put site online
    if (nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach (nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOnlineCommand();
        $cmdLine = sprintf('%s %s %s', $symfonyRootDir, nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_name'), nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_env'));

        $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
      }
    }
    $this->logLine('Symfony project deployed successfully');

    return true;
  }

  private function executeCommand(nbCommand $command, $commandLine, $doit, $verbose) {
    if ($doit) {
      $parser = new nbCommandLineParser();
      $parser->setDefaultConfigurationDirs($this->getParser()->getDefaultConfigurationDirs());

      if (!$command->run($parser, $commandLine))
        throw new Exception('Error executing: ' . $cmd);
    }

    if ($verbose)
      $this->logLine(sprintf("  <comment>Executing command: %s</comment>\n   %s\n", $command->getFullName(), $commandLine));
  }

}
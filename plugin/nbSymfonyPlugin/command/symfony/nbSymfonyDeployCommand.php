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
        new nbArgument('config-file', nbArgument::OPTIONAL, 'Deploy configuration file', './.bee/nbSymfonyPlugin.yml')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    // bee project must be generated
    if (!file_exists('./.bee/')) {
      $this->logLine('No bee project defined!', nbLogger::ERROR);
      $this->logLine('Run: <info>bee bee:generate-project</info>', nbLogger::COMMENT);
      return true;
    }

    $this->logLine('Running: symfony:project-deploy', nbLogger::COMMENT);

    $pluginConfigFile = $arguments['config-file'];
    $doit = isset($options['doit']);

    if (!file_exists($pluginConfigFile)) {
      $cmd = new nbConfigPluginCommand();
      $cmd->run(new nbCommandLineParser(), 'nbSymfonyPlugin');
      $this->logLine('Configuration file "' . $pluginConfigFile . '" was created.', nbLogger::INFO);
      $this->logLine('Modify it and re-run the command.', nbLogger::INFO);
      return true;
    }
    
    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($pluginConfigFile);

    //site offline
    if (nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach (nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOfflineCommand();
        $cmdLine = nbConfig::get('symfony_project-deploy_symfony-exe-path') . ' '
          . nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_name') . ' '
          . nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_env');
        $this->executeCommand($cmd, $cmdLine, $doit);
      }
    }
    //archive site directory
    if (nbConfig::has('archive_inflate-dir')) {
      $cmd = new nbInflateDirCommand();
      $cmdLine = '--config-file=' . $pluginConfigFile;
      $this->executeCommand($cmd, $cmdLine, $doit);
    }
    //sync project
    if (nbConfig::has('filesystem_dir-transfer')) {
      $cmd = new nbDirTransferCommand();
      $cmdLine = '--doit --delete --config-file=' . $pluginConfigFile;
      $this->executeCommand($cmd, $cmdLine, $doit);
    }

    //check dirs
    $cmd = new nbSymfonyCheckDirsCommand();
    $cmdLine = nbConfig::get('symfony_project-deploy_symfony-exe-path');
    $this->executeCommand($cmd, $cmdLine, $doit);

    //check permission
    $cmd = new nbSymfonyCheckPermissionsCommand();
    $cmdLine = nbConfig::get('symfony_project-deploy_symfony-exe-path');
    $this->executeCommand($cmd, $cmdLine, $doit);

    //change ownership
    $cmd = new nbSymfonyChangeOwnershipCommand();
    $cmdLine = nbConfig::get('symfony_project-deploy_site-dir') . ' '
      . nbConfig::get('symfony_project-deploy_site-user') . ' '
      . nbConfig::get('symfony_project-deploy_site-group');
    $this->executeCommand($cmd, $cmdLine, $doit);

    //clear cache
    $cmd = new nbSymfonyClearCacheCommand();
    $cmdLine = nbConfig::get('symfony_project-deploy_symfony-exe-path');
    $this->executeCommand($cmd, $cmdLine, $doit);

    //site online
    if (nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach (nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOnlineCommand();
        $cmdLine = nbConfig::get('symfony_project-deploy_symfony-exe-path') . ' '
          . nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_name') . ' '
          . nbConfig::get('symfony_project-deploy_site-applications_' . $key . '_env');
        $this->executeCommand($cmd, $cmdLine, $doit);
      }
    }
    $this->logLine('Done: symfony:project-deploy', nbLogger::COMMENT);
    return true;
  }

  private function executeCommand(nbCommand $command, $commandLine, $doit = false) {
    if ($doit)
      $command->run(new nbCommandLineParser(), $commandLine);
    else
      $this->logLine("\t<comment>command to execute:</comment> " . $command->getFullName() . " " . $commandLine . "\n");
  }

}
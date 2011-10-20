<?php

class nbSymfonyDiemDeployCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony:diem-project-deploy')
      ->setBriefDescription('Deploys a Diem project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('config-file', nbArgument::REQUIRED, 'Deploy configuration file')
      )));

    $this->setOptions(new nbOptionSet(array(
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Diem Deploy');
    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($arguments['config-file']);
    $symfonyExePath = nbConfig::get('symfony_project-deploy_symfony-root-dir');
    
    //site offline
    if (nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach (nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOfflineCommand();
        $application = nbConfig::get('symfony_project-deploy_site-applications_' . $key . "_name");
        $environment = nbConfig::get('symfony_project-deploy_site-applications_' . $key . "_env");
        $cmd->run(new nbCommandLineParser(), sprintf('%s %s %s', $symfonyExePath, $application, $environment));
      }
    }
    
    //archive site directory
    if (nbConfig::has('archive_inflate-dir')) {
      $cmd = new nbInflateDirCommand();
      $commandLine = '--config-file=' . $arguments['config-file'];
      $cmd->run(new nbCommandLineParser(), $commandLine);
    }

    //dump database
    if (nbConfig::has('mysql_dump')) {
      $cmd = new nbMysqlDumpCommand();
      $commandLine = '--config-file=' . $arguments['config-file'];
      $cmd->run(new nbCommandLineParser(), $commandLine);
    }
    
    //sync project
    if (nbConfig::has('filesystem_dir-transfer')) {
      $cmd = new nbDirTransferCommand();
      $commandLine = '--doit --delete --config-file=' . $arguments['config-file'];
      $cmd->run(new nbCommandLineParser(), $commandLine);
    }

    //check dirs
    $cmd = new nbSymfonyCheckDirsCommand();
    $commandLine = $symfonyExePath;
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //check permission
    $cmd = new nbSymfonyCheckPermissionsCommand();
    $commandLine = $symfonyExePath;
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //change ownership
    $cmd = new nbSymfonyChangeOwnershipCommand();
    $commandLine = sprintf('%s %s %s',
      nbConfig::get('symfony_project-deploy_site-dir'),
      nbConfig::get('symfony_project-deploy_site-user'),
      nbConfig::get('symfony_project-deploy_site-group'));
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //restore database
    if (nbConfig::has('mysql_restore')) {
      $cmd = new nbMysqlRestoreCommand();
      $commandLine = '--config-file=' . $arguments['config-file'];
      $cmd->run(new nbCommandLineParser(), $commandLine);
    }

    //diem setup
    $cmd = new nbSymfonyDiemSetupCommand();
    $commandLine = $symfonyExePath;
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //clear cache
    $cmd = new nbSymfonyClearCacheCommand();
    $commandLine = $symfonyExePath;
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //site online
    if (nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach (nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOnlineCommand();
        $application = nbConfig::get('symfony_project-deploy_site-applications_' . $key . "_name");
        $environment = nbConfig::get('symfony_project-deploy_site-applications_' . $key . "_env");
        $cmd->run(new nbCommandLineParser(), sprintf('%s %s %s', $symfonyExePath, $application, $environment));
      }
    }
    
    $this->logLine('Done - Diem Deploy');
    
    return true;
  }

}
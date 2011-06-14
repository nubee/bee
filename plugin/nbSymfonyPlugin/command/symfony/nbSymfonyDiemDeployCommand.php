<?php

class nbSymfonyDiemDeployCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony:diem-project-deploy')
            ->setBriefDescription('Deploy a Diem project')
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
    //site offline
    if (nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach (nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOfflineCommand();
        $cmd->run(new nbCommandLineParser(), nbConfig::get('symfony_project-deploy_symfony-exe-path') . " " . nbConfig::get('symfony_project-deploy_site-applications_' . $key . "_name") . " " . nbConfig::get('symfony_project-deploy_site-applications_' . $key . "_env"));
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
      $command_line = '--config-file=' . $arguments['config-file'];
      $cmd->run(new nbCommandLineParser(), $command_line);
    }
    //sync project
    if (nbConfig::has('filesystem_dir-transfer')) {
      $cmd = new nbDirTransferCommand();
      $commandLine = '--doit --delete --config-file=' . $arguments['config-file'];
      $cmd->run(new nbCommandLineParser(), $commandLine);
    }

    //check dirs
    $cmd = new nbSymfonyCheckDirsCommand();
    $commandLine = nbConfig::get('symfony_project-deploy_symfony-exe-path');
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //check permission
    $cmd = new nbSymfonyCheckPermissionsCommand();
    $commandLine = nbConfig::get('symfony_project-deploy_symfony-exe-path');
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //change ownership
    $cmd = new nbSymfonyChangeOwnershipCommand();
    $commandLine = nbConfig::get('symfony_project-deploy_site-dir') . ' ' . nbConfig::get('symfony_project-deploy_site-user') . ' ' . nbConfig::get('symfony_project-deploy_site-group');
    $cmd->run(new nbCommandLineParser(), $commandLine);



    //restore database
    if (nbConfig::has('mysql_restore')) {
      $cmd = new nbMysqlRestoreCommand();
      $command_line = '--config-file=' . $arguments['config-file'];
      $cmd->run(new nbCommandLineParser(), $command_line);
    }

    //diem setup
    $cmd = new nbSymfonyDiemSetupCommand();
    $command_line = nbConfig::get('symfony_project-deploy_symfony-exe-path');
    $cmd->run(new nbCommandLineParser(), $command_line);


    //clear cache
    $cmd = new nbSymfonyClearCacheCommand();
    $commandLine = nbConfig::get('symfony_project-deploy_symfony-exe-path');
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //site online
    if (nbConfig::has('symfony_project-deploy_site-applications')) {
      foreach (nbConfig::get('symfony_project-deploy_site-applications') as $key => $value) {
        $cmd = new nbSymfonyGoOnlineCommand();
        $cmd->run(new nbCommandLineParser(), nbConfig::get('symfony_project-deploy_symfony-exe-path') . " " . nbConfig::get('symfony_project-deploy_site-applications_' . $key . "_name") . " " . nbConfig::get('symfony_project-deploy_site-applications_' . $key . "_env"));
      }
    }
    $this->logLine('Done - Diem Deploy');
    return true;
  }

}
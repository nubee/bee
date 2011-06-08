<?php

class nbSymfonyDeployCommand extends nbCommand {

  protected function configure() {
    $this->setName('symfony:project-deploy')
            ->setBriefDescription('Deploy a symfony project')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
);

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('config_file', nbArgument::REQUIRED, 'Deploy configuration file')
            )));

    $this->setOptions(new nbOptionSet(array(
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Symfony Deploy');
    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($arguments['config_file']);
    //site offline
    foreach (nbConfig::get('nbDeploy_site_applications') as $key => $value) {
      $cmd = new nbSymfonyGoOfflineCommand();
      $cmd->run(new nbCommandLineParser(), nbConfig::get('nbDeploy_site_symfony_path') . " " . nbConfig::get('nbDeploy_site_applications_' . $key . "_name") . " " . nbConfig::get('nbDeploy_site_applications_' . $key . "_env"));
    }
    //tar site directory
    $cmd = new nbTarInflateDirCommand();
    $commandLine = nbConfig::get('nbDeploy_tar_target_path') . " " . nbConfig::get('nbDeploy_tar_archive_path') . " " . nbConfig::get('nbDeploy_tar_target_dir');
    $cmd->run(new nbCommandLineParser(), $commandLine);
    //sync project
    $cmd = new nbSymfonySyncProjectCommand();
    $commandLine =  '--doit --exclude-from='.nbConfig::get('nbDeploy_sync_exclude_file').' '.nbConfig::get('nbDeploy_site_source_path').' '.nbConfig::get('nbDeploy_site_dir_path');
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //check dirs
    $cmd = new nbSymfonyCheckDirsCommand();
    $commandLine =  nbConfig::get('nbDeploy_site_symfony_path');
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //check permission
    $cmd = new nbSymfonyCheckPermissionsCommand();
    $commandLine =  nbConfig::get('nbDeploy_site_symfony_path');
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //change ownership
    $cmd = new nbSymfonyChangeOwnershipCommand();
    $commandLine =  nbConfig::get('nbDeploy_site_dir_path').' '.nbConfig::get('nbDeploy_site_user').' '.nbConfig::get('nbDeploy_site_group');
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //clear chache
    $cmd = new nbSymfonyClearCacheCommand();
    $commandLine =  nbConfig::get('nbDeploy_site_symfony_path');
    $cmd->run(new nbCommandLineParser(), $commandLine);

    //site online
    foreach (nbConfig::get('nbDeploy_site_applications') as $key => $value) {
      $cmd = new nbSymfonyGoOnlineCommand();
      $cmd->run(new nbCommandLineParser(), nbConfig::get('nbDeploy_site_symfony_path') . " " . nbConfig::get('nbDeploy_site_applications_' . $key . "_name") . " " . nbConfig::get('nbDeploy_site_applications_' . $key . "_env"));
    }
    $this->logLine('Done - Symfony Deploy');
    return true;
  }

}
<?php

class nbSymfonyDeployCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony:deploy')
            ->setBriefDescription('Deploys a symfony project. (use with sudo)')
            ->setDescription(<<<TXT
Examples:

  Shows the list of commands will run
  <info>./bee symfony:deploy</info>

  Deploys the project (you have to run with sudo)
  <info>./bee symfony:deploy -x</info>
  
  Deploys the project (but reads the configuration from <comment>other-config.yml</comment>)
  <info>./bee symfony:deploy --config-file=.bee/symfony-deploy.yml -x</info>
TXT
    );

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Makes the changes!'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    // bee project must be defined
    if (!is_dir('./.bee') && !file_exists('./bee.yml')) {
      $message = 'No bee project defined!';
      $message .= "\n\n  Run: bee bee:generate-project";

      throw new InvalidArgumentException($message);
    }

    $doit = isset($options['doit']);
    $verbose = isset($options['verbose']) || !$doit;
    
    // Loads configuration
    $configDir = nbConfig::get('nb_plugins_dir') . '/nbSymfonyPlugin/config/';
    $configFilename = isset($options['config-file']) ? $options['config-file'] : '.bee/symfony-deploy.yml';
    $this->loadConfiguration($configDir, $configFilename);

    // Variables from config
    $deployDir = nbConfig::get('deploy_dir');
    $excludeList = nbConfig::get('exclude_list');
    $includeList = nbConfig::get('include_list');
    $backupDir = nbConfig::get('backup_dir');
    $webSourceDir = nbConfig::get('web_source_dir');
    $symfonySourceDir = nbConfig::get('symfony_source_dir');
    $webProdDir = nbConfig::get('web_prod_dir');
    $symfonyProdDir = nbConfig::get('symfony_prod_dir');
    $webUser = nbConfig::get('web_user');
    $webGroup = nbConfig::get('web_group');
    $dbName = nbConfig::get('db_name');
    $dbUser = nbConfig::get('db_user');
    $dbPass = nbConfig::get('db_pass');
    $symfonyEnvironment = nbConfig::get('symfony_environment');
    $symfonyApplications = nbConfig::get('symfony_applications');
    
    $isFirstDeploy = !file_exists(sprintf('%s/symfony', $symfonyProdDir));
    $this->logLine(sprintf('Deploying symfony project %s', !$isFirstDeploy ?: '(First deploy)'));

    // Put applications offline
    if (!$isFirstDeploy) {
      foreach ($symfonyApplications as $application) {
        $cmd = new nbSymfonyGoOfflineCommand();
        $cmdLine = sprintf('%s %s %s', $symfonyProdDir, $application, $symfonyEnvironment);
        $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
      }
    }

    // Archive site directory
    if (!$isFirstDeploy) {
      $cmd = new nbArchiveDirCommand();
      $cmdLine = sprintf('%s %s --create-destination-dir', $deployDir, $backupDir);
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    }

    // Dump database
    if (!$isFirstDeploy) {
      if ($dbName && $dbUser && $dbPass) {
        $cmd = new nbMysqlDumpCommand();
        $cmdLine = sprintf('%s %s %s %s', $dbName, $backupDir, $dbUser, $dbPass);
        $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
      }
    }

    // Sync web directory
    $cmd = new nbDirTransferCommand();
    $cmdLine = sprintf('%s %s --exclude-from=%s --include-from=%s --doit --delete',
      $webSourceDir,
      $webProdDir,
      $excludeList,
      $includeList
    );
    $this->executeCommand($cmd, $cmdLine, $doit, $verbose);

    // Sync symfony directory   
    $cmd = new nbDirTransferCommand();
    $cmdLine = sprintf('%s %s --exclude-from=%s --include-from=%s --doit --delete',
      $symfonySourceDir,
      $symfonyProdDir,
      $excludeList,
      $includeList
    );
    $this->executeCommand($cmd, $cmdLine, $doit, $verbose);

    // Check dirs
    $cmd = new nbSymfonyCheckDirsCommand();
    $this->executeCommand($cmd, $symfonyProdDir, $doit, $verbose);

    // Check permissions
    $cmd = new nbSymfonyCheckPermissionsCommand();
    $this->executeCommand($cmd, $symfonyProdDir, $doit, $verbose);

    // Change dirs ownership
    $cmd = new nbChangeOwnershipCommand();
    $cmdLine = sprintf('%s %s %s --doit', $symfonyProdDir, $webUser, $webGroup);
    try {
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    } catch (Exception $e) {
      $this->logLine('Cannot change permissions', nbLogger::ERROR);
    }
    
    $cmd = new nbChangeOwnershipCommand();
    $cmdLine = sprintf('%s %s %s --doit', $webProdDir, $webUser, $webGroup);
    try {
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    } catch (Exception $e) {
      $this->logLine('Cannot change permissions', nbLogger::ERROR);
    }

    // Clear cache
    $cmd = new nbSymfonyClearCacheCommand();
    $this->executeCommand($cmd, $symfonyProdDir, $doit, $verbose);

    // Put site online
    if (!$isFirstDeploy) {
      foreach (nbConfig::get('symfony_applications') as $application) {
        $cmd = new nbSymfonyGoOnlineCommand();
        $cmdLine = sprintf('%s %s %s', $symfonyProdDir, $application, $symfonyEnvironment);
        $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
      }
    }

    $this->logLine('Symfony project deployed successfully');

    return true;
  }
}
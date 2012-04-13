<?php

class nbWebsiteDeployCommand extends nbApplicationCommand
{

  protected function configure()
  {
    $this->setName('website:deploy')
      ->setBriefDescription('Deploys a generic website project')
      ->setDescription(<<<TXT
Examples:

  Shows the list of commands will run
  <info>./bee website:deploy</info>

  Deploys the project (you have to run with sudo)
  <info>./bee website:deploy -x</info>
  
  Deploys the project (but reads the configuration from <comment>other-config.yml</comment>)
  <info>./bee website:deploy --config-file=.bee/other-config.yml -x</info>
TXT
    );

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Makes the changes!'),
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
    
    $doit = isset($options['doit']);
    $verbose = isset($options['verbose']) || !$doit;

    // Loads configuration
    $configDir = nbConfig::get('nb_plugins_dir') . '/nbWebsitePlugin/config/';
    $configFilename = isset($options['config-file']) ? $options['config-file'] : '.bee/website-deploy.yml';
    $this->loadConfiguration($configDir, $configFilename);

    // Variables from config
    $deployDir = nbConfig::get('deploy_dir');
    $excludeList = nbConfig::get('exclude_list');
    $includeList = nbConfig::get('include_list');
    $backupDir = nbConfig::get('backup_dir');
    $webSourceDir = nbConfig::get('web_source_dir');
    $webProdDir = nbConfig::get('web_prod_dir');
    $webUser = nbConfig::get('web_user');
    $webGroup = nbConfig::get('web_group');
    $dbName = nbConfig::get('db_name');
    $dbUser = nbConfig::get('db_user');
    $dbPass = nbConfig::get('db_pass');
    
    $this->logLine('Deploying website', nbLogger::INFO);
    
    // Archive site directory
    $cmd = new nbArchiveDirCommand();
    $cmdLine = sprintf('%s %s --create-destination-dir', $deployDir, $backupDir);
    $this->executeCommand($cmd, $cmdLine, $doit, $verbose);

    // Dump database
    if ($dbName && $dbUser && $dbPass) {
      $cmd = new nbMysqlDumpCommand();
      $cmdLine = sprintf('%s %s %s %s', $dbName, $backupDir, $dbUser, $dbPass);
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
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
    
    // Change ownership
    $cmd = new nbChangeOwnershipCommand();
    $cmdLine = sprintf('%s %s %s --doit', $webProdDir, $webUser, $webGroup);
    try {
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    } catch (Exception $e) {
      $this->logLine('Cannot change permissions', nbLogger::ERROR);
    }
    
    $this->logLine('Website deployed successfully', nbLogger::INFO);

    return true;
  }
}
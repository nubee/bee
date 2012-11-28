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
            new nbOption('delete', 'd', nbOption::PARAMETER_NONE, 'Enables --delete option in rsync'),
        )));
    }

    protected function execute(array $arguments = array(), array $options = array())
    {
        $this->checkBeeProject();

        $doit = isset($options['doit']);
        $verbose = isset($options['verbose']) || !$doit;

        // Loads configuration
        $configDir = nbConfig::get('nb_plugins_dir') . '/nbWebsitePlugin/config/';
        $configFilename = isset($options['config-file']) ? $options['config-file'] : '.bee/website-deploy.yml';
        $this->loadConfiguration($configDir, $configFilename);

        // Variables from config
        $websiteName = nbConfig::get('website_name');
//    $deployDir = nbConfig::get('deploy_dir');
        $excludeList = nbConfig::get('exclude_list');
        $includeList = nbConfig::get('include_list');
        $backupSources = nbConfig::get('backup_sources');
        $backupDestination = nbConfig::get('backup_destination');
        $webSourceDir = nbConfig::get('web_source_dir');
        $webProdDir = nbConfig::get('web_prod_dir');
        $webUser = nbConfig::get('web_user');
//    $webGroup = nbConfig::get('web_group');
        $dbName = nbConfig::get('db_name');
        $dbUser = nbConfig::get('db_user');
        $dbPass = nbConfig::get('db_pass');

        $this->logLine('Deploying website', nbLogger::INFO);

        // Archive site directory
        $cmd = new nbArchiveCommand();
        $cmdLine = sprintf('%s/%s.tgz %s --add-timestamp --force', $backupDestination, $websiteName, implode(' ', $backupSources));
        $this->executeCommand($cmd, $cmdLine, $doit, $verbose);

        // Dump database
        if ($dbName && $dbUser && $dbPass) {
            $cmd = new nbMysqlDumpCommand();
            $cmdLine = sprintf('%s %s %s %s', $dbName, $backupDestination, $dbUser, $dbPass);
            $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
        }

        // Sync web directory
        $cmd = new nbDirTransferCommand();
        $delete = isset($options['delete']) ? '--delete' : '';
        $cmdLine = sprintf('%s %s --owner=%s --exclude-from=%s --include-from=%s %s %s', $webSourceDir, $webProdDir, $webUser, $excludeList, $includeList, $doit ? '--doit' : '', $delete
        );
        $this->executeCommand($cmd, $cmdLine, true, $verbose);

        $this->logLine('Website deployed successfully', nbLogger::INFO);

        return true;
    }

}
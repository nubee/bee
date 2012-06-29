<?php

class nbSymfony2DeployCommand extends nbApplicationCommand
{

    protected function configure()
    {
        $this->setName('Symfony2:deploy')
            ->setBriefDescription('Deploys a Symfony2 project. (use with sudo)')
            ->setDescription(<<<TXT
Examples:

  Shows the list of commands will run
  <info>./bee Symfony2:deploy</info>

  Deploys the project (you have to run with sudo)
  <info>./bee Symfony2:deploy -x</info>
  
  Deploys the project (but reads the configuration from <comment>other-config.yml</comment>)
  <info>./bee Symfony2:deploy --config-file=.bee/symfony2-deploy.yml -x</info>
TXT
        );

        $this->setOptions(new nbOptionSet(array(
                new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Makes the changes!'),
                new nbOption('delete', 'd', nbOption::PARAMETER_NONE, 'Enables --delete option in rsync'),
            )));
    }

    protected function execute(array $arguments = array(), array $options = array())
    {
        // bee project must be defined
        if (!is_dir('./.bee') && !file_exists('./bee.yml')) {
            $message = 'No bee project defined!';
            $message .= "\n\n  Run: bee bee:generate-project";

            throw new InvalidArgumentException($message);
        }

        $doit = isset($options['doit']);
        $verbose = isset($options['verbose']) || !$doit;

        // Loads configuration
        $configDir = nbConfig::get('nb_plugins_dir') . '/nbSymfony2Plugin/config/';
        $configFilename = isset($options['config-file']) ? $options['config-file'] : '.bee/symfony2-deploy.yml';
        $this->loadConfiguration($configDir, $configFilename);

        // Variables from config
        $websiteName = nbConfig::get('website_name');
//        $deployDir = nbConfig::get('deploy_dir');
        $excludeList = nbConfig::get('exclude_list');
        $includeList = nbConfig::get('include_list');
        $backupSources = nbConfig::get('backup_sources');
        $backupDestination = nbConfig::get('backup_destination');
        $webSourceDir = nbConfig::get('web_source_dir');
        $symfonySourceDir = nbConfig::get('symfony_source_dir');
        $webProdDir = nbConfig::get('web_prod_dir');
        $symfonyProdDir = nbConfig::get('symfony_prod_dir');
        $webUser = nbConfig::get('web_user');
//        $webGroup = nbConfig::get('web_group');
        $dbName = nbConfig::get('db_name');
        $dbUser = nbConfig::get('db_user');
        $dbPass = nbConfig::get('db_pass');
        $symfonyEnvironment = nbConfig::get('symfony_environment');

        $isFirstDeploy = !file_exists(sprintf('%s/app/console', $symfonyProdDir)) || !file_exists(sprintf('%s/vendor/symfony', $symfonyProdDir));
        $this->logLine(sprintf('Deploying symfony project %s', !$isFirstDeploy ? '' : '(First deploy)'), nbLogger::INFO);

        // Archive site directory
        if (!$isFirstDeploy) {
            $cmd = new nbArchiveCommand();
            $cmdLine = sprintf('%s/%s.tgz %s --add-timestamp --force', $backupDestination, $websiteName, implode(' ', $backupSources));
            $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
        }

        // Dump database
        if (!$isFirstDeploy) {
            if ($dbName && $dbUser && $dbPass) {
                $cmd = new nbMysqlDumpCommand();
                $cmdLine = sprintf('%s %s %s %s', $dbName, $backupDestination, $dbUser, $dbPass);
                $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
            }
        }

        // --delete option for sync
        $delete = isset($options['delete']) ? '--delete' : '';

        // Sync web directory
        $cmd = new nbDirTransferCommand();
        $cmdLine = sprintf('%s %s --owner=%s --exclude-from=%s --include-from=%s %s %s',
            $webSourceDir,
            $webProdDir,
            $webUser,
            $excludeList,
            $includeList,
            $doit ? '--doit' : '',
            $delete
        );
        $this->executeCommand($cmd, $cmdLine, true, $verbose);

        // Sync symfony directory
        $cmd = new nbDirTransferCommand();
        $cmdLine = sprintf('%s %s --owner=%s --exclude-from=%s --include-from=%s %s %s',
            $symfonySourceDir,
            $symfonyProdDir,
            $webUser,
            $excludeList,
            $includeList,
            $doit ? '--doit' : '',
            $delete
        );
        $this->executeCommand($cmd, $cmdLine, true, $verbose);

        // Intall vendors (php bin/vendors install)
        if ($isFirstDeploy) {
            $cmdLine = sprintf('php %s/bin/vendors install', $symfonyProdDir);
            $this->executeShellCommand($cmdLine, $doit);
        } else {
            // Clear cache
            $cmdLine = sprintf('php %s/app/console cache:clear --env=%s', $symfonyProdDir, $symfonyEnvironment);
            $this->executeShellCommand($cmdLine, $doit);

            // Publish assets
            $cmdLine = sprintf('php %s/app/console assets:install %s --env=%s', $symfonyProdDir, $webProdDir, $symfonyEnvironment);
            $this->executeShellCommand($cmdLine, $doit);
        }

        $cmdLine = sprintf('php %s/app/console assetic:dump --no-debug --env=%s', $symfonyProdDir, $symfonyEnvironment);
        $this->executeShellCommand($cmdLine, $doit);

        $this->logLine('Symfony2 project deployed successfully', nbLogger::INFO);

        return true;
    }

}
<?php

class nbSymfony21DeployCommand extends nbApplicationCommand
{

    protected function configure()
    {
        $this->setName('Symfony21:deploy')
            ->setBriefDescription('Deploys a Symfony 2.1 project. (use with sudo)')
            ->setDescription(<<<TXT
** Execute with sudo **

Examples:

  Shows the list of commands will run
  <info>./bee Symfony21:deploy</info>

  Deploys the project
  <info>./bee Symfony21:deploy -x -d</info>

  Deploys the project with no dump and no backup
  <info>./bee Symfony21:deploy -x -d --no-dump --no-backup</info>
TXT
        );

        $this->setOptions(new nbOptionSet(array(
            new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Makes the changes!'),
            new nbOption('delete', 'd', nbOption::PARAMETER_NONE, 'Enables --delete option in rsync'),
            new nbOption('no-backup', '', nbOption::PARAMETER_NONE, 'Disable directories backup'),
            new nbOption('no-dump', '', nbOption::PARAMETER_NONE, 'Disable database dump'),
        )));
    }

    protected function execute(array $arguments = array(), array $options = array())
    {
        $this->checkBeeProject();

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
        $webGroup = nbConfig::get('web_group');
        $dbName = nbConfig::get('db_name');
        $dbUser = nbConfig::get('db_user');
        $dbPass = nbConfig::get('db_pass');
        $symfonyEnvironment = nbConfig::get('symfony_environment');

        $isFirstDeploy = !file_exists(sprintf('%s/app/console', $symfonyProdDir)) || !file_exists(sprintf('%s/vendor/symfony', $symfonyProdDir));
        $this->logLine(sprintf('Deploying symfony project %s', !$isFirstDeploy ? '' : '(First deploy)'), nbLogger::INFO);

        // Archive site directory
        if (!$isFirstDeploy && !isset($options['no-backup'])) {
            $cmd = new nbArchiveCommand();
            $cmdLine = sprintf('%s/%s.tgz %s --add-timestamp --force', $backupDestination, $websiteName, implode(' ', $backupSources));
            $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
        }

        // Dump database
        if (!$isFirstDeploy) {
            if ($dbName && $dbUser && $dbPass && !isset($options['no-dump'])) {
                $cmd = new nbMysqlDumpCommand();
                $cmdLine = sprintf('%s %s %s %s', $dbName, $backupDestination, $dbUser, $dbPass);
                $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
            }
        }

        // --delete option for sync
        $delete = isset($options['delete']) ? '--delete' : '';

        // Sync web directory
        $cmd = new nbDirTransferCommand();
        $cmdLine = sprintf('%s %s --owner=%s --exclude-from=%s --include-from=%s %s %s', $webSourceDir, $webProdDir, $webUser, $excludeList, $includeList, $doit ? '--doit' : '', $delete
        );
        $this->executeCommand($cmd, $cmdLine, true, $verbose);

        // Sync symfony directory
        $cmd = new nbDirTransferCommand();
        $cmdLine = sprintf('%s %s --owner=%s --exclude-from=%s --include-from=%s %s %s', $symfonySourceDir, $symfonyProdDir, $webUser, $excludeList, $includeList, $doit ? '--doit' : '', $delete
        );
        $this->executeCommand($cmd, $cmdLine, true, $verbose);

        if ($isFirstDeploy) {
            // Copy parameters.yml
            $parametersSource = sprintf('%s/app/config/parameters.yml.dist', $symfonySourceDir);
            $parametersDestination = sprintf('%s/app/config/parameters.yml', $symfonyProdDir);
            if ($doit) {
                $this->getFileSystem()->copy($parametersSource, $parametersDestination);
            } else {
                $this->logLine(sprintf('Copy file %s to %s', $parametersSource, $parametersDestination), nbLogger::INFO);
            }
        } else {
            // Download composer
            if (!file_exists(sprintf('%s/composer.phar', $symfonyProdDir))) {
                $cmdLine = sprintf('curl -s https://getcomposer.org/installer | php -- --install-dir=%s', $symfonyProdDir);
                $this->executeShellCommand($cmdLine, $doit);
            }

            // Run composer
            $cmdLine = sprintf('php %1$s/composer.phar --working-dir=%1$s install', $symfonyProdDir);
            $this->executeShellCommand($cmdLine, $doit);

            // Publish assets
            $cmdLine = sprintf('php %s/app/console assets:install %s --env=%s', $symfonyProdDir, $webProdDir, $symfonyEnvironment);
            $this->executeShellCommand($cmdLine, $doit);

            // Clear cache
            $cmdLine = sprintf('php %s/app/console cache:clear --env=%s --no-debug', $symfonyProdDir, $symfonyEnvironment);
            $this->executeShellCommand($cmdLine, $doit);
        }

        $cmdLine = sprintf('php %s/app/console assetic:dump --no-debug --env=%s', $symfonyProdDir, $symfonyEnvironment);
        $this->executeShellCommand($cmdLine, $doit);

        if ($doit) {
            $this->getFileSystem()->chmodRecursive($webProdDir, 0755, 0755);
            $this->getFileSystem()->chownRecursive($webProdDir, $webUser, $webGroup);
            $this->getFileSystem()->chmodRecursive($symfonyProdDir, 0755, 0755);
            $this->getFileSystem()->chownRecursive($symfonyProdDir, $webUser, $webGroup);
        }

        $this->logLine('Symfony 2.1 project deployed successfully', nbLogger::INFO);

        return true;
    }

}
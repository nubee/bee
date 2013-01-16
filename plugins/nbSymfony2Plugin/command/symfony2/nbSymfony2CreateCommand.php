<?php

class nbSymfony2CreateCommand extends nbApplicationCommand
{

    protected function configure()
    {
        $this->setName('Symfony2:create')
            ->setBriefDescription('** ALPHA VERSION ** Creates a Symfony2 project')
        ;

        $this->setArguments(new nbArgumentSet(array(
            new nbArgument('project-base-dir', nbArgument::OPTIONAL, 'Project base directory', './'),
        )));

        $this->setOptions(new nbOptionSet(array(
            new nbOption('sf2-version', '', nbOption::PARAMETER_REQUIRED, 'es: 2.1.x, 2.1.6'),
//            new nbOption('web-dir', '', nbOption::PARAMETER_REQUIRED, 'Changes the name of the web directory (if not specified default is "httpdocs")'),
            new nbOption('db-name', '', nbOption::PARAMETER_REQUIRED, 'To create a database use with --db-user AND --db-pass; to populate an existing database use with --db-dump-file'),
            new nbOption('db-user', '', nbOption::PARAMETER_REQUIRED, 'The user of the database (requires --db-name and --db-pass)'),
            new nbOption('db-pass', '', nbOption::PARAMETER_REQUIRED, 'The password for the user of the database (requires --db-name and --db-user)'),
            new nbOption('mysql-user', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root user', 'root'),
            new nbOption('mysql-pass', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root password', 'root'),
        )));
    }

    protected function execute(array $arguments = array(), array $options = array())
    {
        $this->logLine('Creating Symfony2 project', nbLogger::INFO);

        $projectBaseDir = nbFileSystem::sanitizeDir($arguments['project-base-dir']);

        // Download Symfony framework
        $cmd = sprintf('composer create-project --no-interaction symfony/framework-standard-edition %s/Symfony 2.1.x-dev', $projectBaseDir);
        $this->executeShellCommand($cmd);

        // TODO: move web to httpdocs

        // TODO: change paths in app.php, app_dev.php, app/config/config.yml

        // TODO: generate the virtual host

        // Permissions on app/cache and app/logs
        $cmd = sprintf('sudo setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx %1$s/Symfony/app/cache %1$s/Symfony/app/logs', $projectBaseDir);
        $this->executeShellCommand($cmd);
        $cmd = sprintf('sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx %1$s/Symfony/app/cache %1$s/Symfony/app/logs', $projectBaseDir);
        $this->executeShellCommand($cmd);

        // Create the database
        $dbName = isset($options['db-name']) ? $options['db-name'] : null;
        $dbUser = isset($options['db-user']) ? $options['db-user'] : null;
        $dbPass = isset($options['db-pass']) ? $options['db-pass'] : null;
        $mysqlUser = isset($options['mysql-user']) ? $options['mysql-user'] : 'root';
        $mysqlPass = isset($options['mysql-pass']) ? $options['mysql-pass'] : 'root';

        if ($dbName && $dbUser && $dbPass) {
            $cmdLine = sprintf('%s %s %s --username=%s --password=%s', $dbName, $mysqlUser, $mysqlPass, $dbUser, $dbPass);
            $cmd = new nbMysqlCreateCommand();
            $this->executeCommand($cmd, $cmdLine, true, false);
        }

        return true;
    }

}
<?php

class nbSymfonyInitCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony:init')
            ->setBriefDescription('Initliazes a symfony website project (creates and restores database, makes directories for symfony application)')
            ->setDescription(<<<TXT
Examples:

  Creates the deploy dir, the web dir and the symfony dir (if they do not exist) and enables plugins
  <info>./bee symfony:init /var/www/website.com</info>

  Creates the database and the user (mysql user and pass are usually required)
  <info>./bee symfony:init /var/www/website.com --db-name=dbname --db-user=dbuser --db-pass=dbPaZZ --mysql-user=root --mysql-pass=Pa55</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('deploy-dir', nbArgument::REQUIRED, 'The production application directory (ie: /var/www/website.com, /var/www/website.com/subdomains/beta)')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('change-web-dir', '', nbOption::PARAMETER_REQUIRED, 'Changes the name of the web directory (if not specified default is "httpdocs")'),
        new nbOption('change-sf-dir', '', nbOption::PARAMETER_REQUIRED, 'Changes the name of the symfony directory (if not specified default is "symfony")'),
        new nbOption('db-name', '', nbOption::PARAMETER_REQUIRED, 'If specified creates the database'),
        new nbOption('db-user', '', nbOption::PARAMETER_REQUIRED, 'The user of the database (requires --db-name)'),
        new nbOption('db-pass', '', nbOption::PARAMETER_REQUIRED, 'The password for the user of the database (requires --db-name and --db-user)'),
        new nbOption('db-dump-file', '', nbOption::PARAMETER_REQUIRED, 'Dump file used to populate the database (requires --db-name)'),
        new nbOption('mysql-user', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root user', 'root'),
        new nbOption('mysql-pass', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root password', ''),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Initialising symfony website', nbLogger::COMMENT);

    // bee project must be defined
    if(!is_dir('./.bee') && !file_exists('./bee.yml')) {
      $message = 'No bee project defined!';
      $message .= "\n\n  Run: bee bee:generate-project";

      throw new InvalidArgumentException($message);
    }
    
    $verbose = isset($options['verbose']);
    
    // Enable required plugins for website:deploy
    $cmd = new nbEnablePluginCommand();
    $cmdLine = 'nbArchivePlugin --no-configuration';
    $this->executeCommand($cmd, $cmdLine, true, $verbose);
    $cmdLine = 'nbFileSystemPlugin --no-configuration';
    $this->executeCommand($cmd, $cmdLine, true, $verbose);
    $cmdLine = 'nbMysqlPlugin --no-configuration';
    $this->executeCommand($cmd, $cmdLine, true, $verbose);
    $cmdLine = 'nbSymfonyPlugin -f';
    $this->executeCommand($cmd, $cmdLine, true, $verbose);
    
    // Makes app directory
    $deployDir = nbFileSystem::sanitizeDir($arguments['deploy-dir']);
    $webDir = isset($options['change-web-dir']) ? $options['change-web-dir'] : 'httpdocs';
    $webDir = sprintf('%s/%s', $deployDir, $webDir);
    
    if (!is_dir($webDir)) {
      $this->getFileSystem()->mkdir($webDir, true);
    }
    
    $symfonyDir = isset($options['change-sf-dir']) ? $options['change-sf-dir'] : 'symfony';
    $symfonyDir = sprintf('%s/%s', $deployDir, $symfonyDir);
    
    if (!is_dir($symfonyDir)) {
      $this->getFileSystem()->mkdir($symfonyDir, true);
    }

    // Creates the database
    $dbName = isset($options['db-name']) ? $options['db-name'] : null;
    $dbUser = isset($options['db-user']) ? $options['db-user'] : null;
    $dbPass = isset($options['db-pass']) ? $options['db-pass'] : null;
    $mysqlUser = isset($options['mysql-user']) ? $options['mysql-user'] : 'root';
    $mysqlPass = isset($options['mysql-pass']) ? $options['mysql-pass'] : '';
    
    if ($dbName && $dbUser && $dbPass) {
      $cmdLine = sprintf('%s %s %s --username=%s --password=%s', $dbName, $mysqlUser, $mysqlPass, $dbUser, $dbPass);
      $cmd = new nbMysqlCreateCommand();
      $this->executeCommand($cmd, $cmdLine, true, $verbose);
    }
    
    // Restores the database
    $dbDumpFile = isset($options['db-dump-file']) ? $options['db-dump-file'] : null;
    
    if (is_file($dbDumpFile)) {
      if (!$dbName) {
        $this->logLine('You must specify the database name (use option: --db-name)', nbLogger::ERROR);
        return false;
      }
      
      $cmd = new nbMysqlRestoreCommand();
      $cmdLine = sprintf('%s %s %s %s', $dbName, $dbDumpFile, $mysqlUser, $mysqlPass);
      $this->executeCommand($cmd, $cmdLine, true, $verbose);
    }
    
    $this->logLine('Symfony website initialized successfully');

    return true;
  }
}
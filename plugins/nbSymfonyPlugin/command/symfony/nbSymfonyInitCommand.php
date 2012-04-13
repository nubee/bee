<?php

class nbSymfonyInitCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony:init')
            ->setBriefDescription('Initliazes a symfony website project (creates and restores database, makes directories for symfony application)')
            ->setDescription(<<<TXT
Examples:

  Enables plugins required by <info>symfony:deploy</info>
  <info>./bee symfony:init</info>
  
  Creates the deploy dir, the web dir and the symfony dir (if they do not exist)
  <info>./bee symfony:init /var/www/website.com</info>

  Creates the database and the user (mysql user and pass are usually required)
  Database will be created ONLY if <comment>--db-name</comment>, <comment>--db-user</comment>, <comment>--db-pass</comment> options are specified
  <info>./bee symfony:init --db-name=dbname --db-user=dbuser --db-pass=dbPaZZ --mysql-user=root --mysql-pass=Pa55</info>
  
  Populates the database (use mysql user and pass)
  <info>./bee symfony:init --db-name=dbname --db-dump-file=/my/project/db/dump.sql --mysql-user=root --mysql-pass=Pa55</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('deploy-dir', nbArgument::OPTIONAL, 'The production application directory (ie: /var/www/website.com, /var/www/website.com/subdomains/beta)')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('change-web-dir', '', nbOption::PARAMETER_REQUIRED, 'Changes the name of the web directory (if not specified default is "httpdocs")'),
        new nbOption('change-sf-dir', '', nbOption::PARAMETER_REQUIRED, 'Changes the name of the symfony directory (if not specified default is "symfony")'),
        new nbOption('db-name', '', nbOption::PARAMETER_REQUIRED, 'To create a database use with --db-user AND --db-pass; to populate an existing database use with --db-dump-file'),
        new nbOption('db-user', '', nbOption::PARAMETER_REQUIRED, 'The user of the database (requires --db-name and --db-pass)'),
        new nbOption('db-pass', '', nbOption::PARAMETER_REQUIRED, 'The password for the user of the database (requires --db-name and --db-user)'),
        new nbOption('db-dump-file', '', nbOption::PARAMETER_REQUIRED, 'Dump file used to populate the database (requires --db-name)'),
        new nbOption('mysql-user', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root user', 'root'),
        new nbOption('mysql-pass', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root password', ''),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Initialising symfony website', nbLogger::INFO);

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
    
    // Makes deploy directories
    $deployDir = isset($arguments['deploy-dir']) ? nbFileSystem::sanitizeDir($arguments['deploy-dir']) : null;
    if ($deployDir) {
      $webDir = isset($options['change-web-dir']) ? $options['change-web-dir'] : 'httpdocs';
      $webDir = sprintf('%s/%s', $deployDir, $webDir);

      $this->logLine('... Creating/Checking dir' . $webDir, nbLogger::INFO);
      if (!is_dir($webDir)) {
        $this->getFileSystem()->mkdir($webDir, true);
      }

      $symfonyDir = isset($options['change-sf-dir']) ? $options['change-sf-dir'] : 'symfony';
      $symfonyDir = sprintf('%s/%s', $deployDir, $symfonyDir);

      $this->logLine('... Creating/Checking dir' . $symfonyDir, nbLogger::INFO);
      if (!is_dir($symfonyDir)) {
        $this->getFileSystem()->mkdir($symfonyDir, true);
      }
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
    
    $this->logLine('Symfony website initialized successfully', nbLogger::INFO);

    return true;
  }
}
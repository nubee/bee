<?php

class nbWebsiteInitCommand extends nbApplicationCommand
{

  protected function configure()
  {
    $this->setName('website:init')
      ->setBriefDescription('Initliazes a generic website project (creates and restores database, makes directories for website application)')
      ->setDescription(<<<TXT
Examples:

  Creates the deploy dir and the web dir (if they do not exist) and enables plugins
  <info>./bee website:init /var/www/website.com</info>

  Creates the database and the user (mysql user and pass are usually required)
  <info>./bee website:init /var/www/website.com --db-name=dbname --db-user=dbuser --db-pass=dbPaZZ --mysql-user=root --mysql-pass=Pa55</info>
TXT
    );
  
    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('deploy-dir', nbArgument::REQUIRED, 'The production application directory (ie: /var/www/website.com, /var/www/website.com/subdomains/beta)')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('change-web-dir', '', nbOption::PARAMETER_REQUIRED, 'Changes the name of the web dirctory (if not specified default is "httpdocs")'),
        new nbOption('db-name', '', nbOption::PARAMETER_REQUIRED, 'If specified creates the database'),
        new nbOption('db-user', '', nbOption::PARAMETER_REQUIRED, 'The user of the database (requires --db-name)'),
        new nbOption('db-pass', '', nbOption::PARAMETER_REQUIRED, 'The password for the user of the database (requires --db-name and --db-user)'),
        new nbOption('db-dump-file', '', nbOption::PARAMETER_REQUIRED, 'Dump file used to populate the database'),
        new nbOption('mysql-user', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root user', 'root'),
        new nbOption('mysql-pass', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root password', ''),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Initialising website');

    // bee project must be defined
    if(!is_dir('./.bee') && !file_exists('./bee.yml')) {
      $message = 'No bee project defined!';
      $message .= "\n\n  Run: bee bee:generate-project";

      throw new InvalidArgumentException($message);
    }
    
    // Enable required plugins for website:deploy
    $cmd = new nbEnablePluginCommand();
    $cmdLine = 'nbArchivePlugin -f';
    $this->executeCommand($cmd, $cmdLine, true, false);
    $cmdLine = 'nbFileSystemPlugin -f';
    $this->executeCommand($cmd, $cmdLine, true, false);
    $cmdLine = 'nbMysqlPlugin -f';
    $this->executeCommand($cmd, $cmdLine, true, false);
    $cmdLine = 'nbWebsitePlugin -f';
    $this->executeCommand($cmd, $cmdLine, true, false);
    
    $this->executeShellCommand('rm -f ./.bee/archive-*');
    $this->executeShellCommand('rm -f ./.bee/filesystem-*');
    $this->executeShellCommand('rm -f ./.bee/mysql-*');
    
    // Makes web directory
    $deployDir = nbFileSystem::sanitizeDir($arguments['deploy-dir']);
    $webDir = isset($options['change-web-dir']) ? $options['change-web-dir'] : 'httpdocs';
    $appDirectoy = sprintf('%s/%s', $deployDir, $webDir);
    
    if (!is_dir($appDirectoy)) {
      $this->getFileSystem()->mkdir($appDirectoy, true);
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
      $this->executeCommand($cmd, $cmdLine, true, false);
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
      $this->executeCommand($cmd, $cmdLine, true, false);
    }
    
    $this->logLine('Website initialized successfully');

    return true;
  }
}
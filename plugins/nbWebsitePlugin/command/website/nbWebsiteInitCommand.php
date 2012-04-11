<?php

class nbWebsiteInitCommand extends nbApplicationCommand
{

  protected function configure()
  {
    $this->setName('website:init')
      ->setBriefDescription('Initliazes a generic website project (creates and restores database, makes directories for website application)')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );
  
    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('deploy-dir', nbArgument::REQUIRED, 'The production application directory (ie: /var/www/website.com, /var/www/website.com/subdomains/beta)')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('change-web-dir', '', nbOption::PARAMETER_REQUIRED, 'Changes the name of the web dirctory (if not specified default is "httpdocs")'),
        new nbOption('db-params', '', nbOption::PARAMETER_REQUIRED, 'If specified creates the database. The option param must be like this: "name:the_database user:the_user pass:the_pass"'),
        new nbOption('db-dump-file', '', nbOption::PARAMETER_REQUIRED, 'Dump file used to populate the database'),
        new nbOption('mysql-user', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root user', 'root'),
        new nbOption('mysql-pass', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root password', ''),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Initialising website', nbLogger::COMMENT);

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
    
    // Makes app directory
    $deployDir = nbFileSystem::sanitizeDir($arguments['deploy-dir']);
    $webDir = isset($options['change-web-dir']) ? $options['change-web-dir'] : 'httpdocs';
    $appDirectoy = sprintf('%s/%s', $deployDir, $webDir);
    
    if (!is_dir($appDirectoy)) {
      $this->getFileSystem()->mkdir($appDirectoy, true);
    }

    // Creates the database
    $dbParams = isset($options['db-params']) ? $options['db-params'] : null;
    $mysqlUser = isset($options['mysql-user']) ? $options['mysql-user'] : 'root';
    $mysqlPass = isset($options['mysql-pass']) ? $options['mysql-pass'] : '';
    
    if($dbParams) {
      preg_match('/name:(.+) user:(.+) pass:(.+)/', $dbParams, $params);
      $cmdLine = sprintf('%s %s %s --username=%s --password=%s', $params[1], $mysqlUser, $mysqlPass, $params[2], $params[3]);
      $cmd = new nbMysqlCreateCommand();
      $this->executeCommand($cmd, $cmdLine, true, false);
    }
    
    // Restores the database
    $dbDumpFile = isset($options['db-dump-file']) ? $options['db-dump-file'] : null;
    
    if(is_file($dbDumpFile)) {
      $cmd = new nbMysqlRestoreCommand();
      $cmdLine = sprintf('%s %s %s %s', $dbName, $dbDumpFile, $dbUser, $dbPass);
      $this->executeCommand($cmd, $cmdLine, true, false);
    }
    
    $this->logLine('Website initialized successfully');

    return true;
  }

  private function executeCommand(nbCommand $command, $commandLine, $doit, $verbose)
  {
    if($doit) {
      $parser = new nbCommandLineParser();
      $parser->setDefaultConfigurationDirs($this->getParser()->getDefaultConfigurationDirs());

      if(!$command->run($parser, $commandLine))
        throw new Exception('Error executing: ' . $cmd);
    }

    if($verbose)
      $this->logLine(sprintf("  <comment>Executing command: %s</comment>\n   %s\n", $command->getFullName(), $commandLine));
  }

}
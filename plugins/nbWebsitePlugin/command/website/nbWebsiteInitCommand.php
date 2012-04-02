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
        new nbArgument('app-name', nbArgument::REQUIRED, 'Application name'),
        new nbArgument('web-base-dir', nbArgument::REQUIRED, 'Directory that contains the application')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('db-name', '', nbOption::PARAMETER_REQUIRED, 'The database required by the application'),
        new nbOption('db-user', '', nbOption::PARAMETER_REQUIRED, 'The database user'),
        new nbOption('db-pass', '', nbOption::PARAMETER_REQUIRED, 'The database user password'),
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
    $appName = $arguments['app-name'];
    $webBaseDir = nbFileSystem::sanitizeDir($arguments['web-base-dir']);
    
    $appDirectoy = sprintf('%s/%s/httpdocs', $webBaseDir, $appName);
    
    if (!is_dir($appDirectoy)) {
      $this->getFileSystem()->mkdir($appDirectoy, true);
    }

    // Creates the database
    $dbName = isset($options['db-name']) ? $options['db-name'] : null;
    $dbUser = isset($options['db-user']) ? $options['db-user'] : null;
    $dbPass = isset($options['db-pass']) ? $options['db-pass'] : null;
    $mysqlUser = isset($options['mysql-user']) ? $options['mysql-user'] : 'root';
    $mysqlPass = isset($options['mysql-pass']) ? $options['mysql-pass'] : '';
    
    if($dbName && $dbUser && $dbPass) {
      $cmd = new nbMysqlCreateCommand();
      $cmdLine = sprintf('%s %s %s --username=%s --password=%s', $dbName, $mysqlUser, $mysqlPass, $dbUser, $dbPass);
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
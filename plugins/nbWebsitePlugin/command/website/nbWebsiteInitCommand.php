<?php

class nbWebsiteInitCommand extends nbApplicationCommand
{

  protected function configure()
  {
    $this->setName('website:init')
      ->setBriefDescription('Initliazes a generic website project (creates and restores database, makes directories for backup and website app')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setOptions(new nbOptionSet(array(
        new nbOption('mysql-user', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root user', 'root'),
        new nbOption('mysql-pass', '', nbOption::PARAMETER_OPTIONAL, 'The mysql root password', ''),
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
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
    $cmdLine = 'nbArchivePlugin';
    $this->executeCommand($cmd, $cmdLine, true, true);
    $cmdLine = 'nbFileSystemPlugin';
    $this->executeCommand($cmd, $cmdLine, true, true);
    $cmdLine = 'nbMysqlPlugin';
    $this->executeCommand($cmd, $cmdLine, true, true);
    $cmdLine = 'nbWebsitePlugin';
    $this->executeCommand($cmd, $cmdLine, true, true);
    
    // Deletes non required config files
    $this->getFileSystem()->delete('archive-*');
    $this->getFileSystem()->delete('filesystem-*');
    $this->getFileSystem()->delete('mysql-*');
    
    if(!isset($options['config-file']))
      throw new Exception('--config-file option required (CHANGE THIS)');

    $doit = isset($options['doit']);
    $verbose = isset($options['verbose']) || !$doit;

    $configDir = nbConfig::get('nb_plugins_dir') . '/nbWebsitePlugin/config/';
    $configFilename = $options['config-file'];
    
    $this->loadConfiguration($configDir, $configFilename);

    // Makes app directory
    $appDirectoy = sprintf('%s/%s/httpdocs', nbConfig::get('web_base_dir'), nbConfig::get('app_name'));
    $this->getFileSystem()->mkdir($appDirectoy, true);

    // Creates the database
    $dbName = nbConfig::get('database_name');
    $dbUser = nbConfig::get('database_user');
    $dbPass = nbConfig::get('database_pass');
    $mysqlUser = $options['mysql-user'];
    $mysqlPass = $options['mysql-pass'];
    
    if($dbName && $dbUser && $dbPass) {
      $cmd = new nbMysqlCreateCommand();
      $cmdLine = sprintf('%s --doit');
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    }
    
    // Database dump
    if(nbConfig::has('mysql_dump')) {
      $cmd = new nbMysqlDumpCommand();
      $cmdLine = '--config-file=' . $configFilename;
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    }
    
    $this->logLine('Website deployed successfully');

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
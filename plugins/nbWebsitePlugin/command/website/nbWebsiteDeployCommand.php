<?php

class nbWebsiteDeployCommand extends nbApplicationCommand
{

  protected function configure()
  {
    $this->setName('website:deploy')
      ->setBriefDescription('Deploys a generic website project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Deploying website', nbLogger::COMMENT);

    // bee project must be defined
    if(!is_dir('./.bee') && !file_exists('./bee.yml')) {
      $message = 'No bee project defined!';
      $message .= "\n\n  Run: bee bee:generate-project";

      throw new InvalidArgumentException($message);
    }
    
    if(!isset($options['config-file']))
      throw new Exception('--config-file option required (CHANGE THIS)');

    $doit = isset($options['doit']);
    $verbose = isset($options['verbose']) || !$doit;

    $configDir = nbConfig::get('nb_plugins_dir') . '/nbWebsitePlugin/config/';
    $configFilename = $options['config-file'];
    
    $this->loadConfiguration($configDir, $configFilename);

    // Archive site directory
    if(nbConfig::has('archive_archive-dir')) {
      $cmd = new nbArchiveDirCommand();
      $cmdLine = sprintf('--config-file=%s --create-destination-dir', $configFilename);
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    }

    // Sync project
    if(nbConfig::has('filesystem_dir-transfer')) {
      $cmd = new nbDirTransferCommand();
      $cmdLine = '--doit --delete --config-file=' . $configFilename;
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    }

    // Change ownership
    if(nbConfig::has('filesystem_change-ownership')) {
      $cmd = new nbChangeOwnershipCommand();
      $cmdLine = '--doit --config-file=' . $configFilename;
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
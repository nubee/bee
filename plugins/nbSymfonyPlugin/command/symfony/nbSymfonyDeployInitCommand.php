<?php

class nbSymfonyDeployInitCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony:project-deploy-init')
            ->setBriefDescription('Deploys a symfony project with initialization of the enviroment')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
            )));


    $this->setOptions(new nbOptionSet(array(
                new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Deploying symfony project', nbLogger::COMMENT);

    // bee project must be defined
    if (!is_dir('./.bee') && !file_exists('./bee.yml')) {
      $message = 'No bee project defined!';
      $message .= "\n\n  Run: bee bee:generate-project";

      throw new InvalidArgumentException($message);
    }

    $doit = isset($options['doit']);
    $verbose = isset($options['verbose']) || !$doit;
    
    // Load configuration
    if (!isset($options['config-file']))
      throw new Exception('--config-file option required (CHANGE THIS)');

    $configDir = nbConfig::get('nb_plugins_dir') . '/nbSymfonyPlugin/config/';
    $configFilename = $options['config-file'];

    $this->loadConfiguration($configDir, $configFilename);

    // Sync project
    if (nbConfig::has('filesystem_dir-transfer')) {
      $cmd = new nbDirTransferCommand();
      $cmdLine = sprintf('%s --config-file=%s', $doit?'--doit':'', $configFilename);
      $parser = new nbCommandLineParser();
      $parser->setDefaultConfigurationDirs($this->getParser()->getDefaultConfigurationDirs());
      if (!$cmd->run($parser, $cmdLine))
        throw new Exception('Error executing: ' . $cmd);
    }
    
    $symfonyRootDir = nbConfig::get('symfony_project-deploy-init_symfony-root-dir');

    // Check dirs
    $cmd = new nbSymfonyCheckDirsCommand();
    $this->executeCommand($cmd, $symfonyRootDir, $doit, $verbose);

    // Check permissions
    $cmd = new nbSymfonyCheckPermissionsCommand();
    $this->executeCommand($cmd, $symfonyRootDir, $doit, $verbose);

    // Change ownership
    $cmd = new nbChangeOwnershipCommand();
    $cmdLine = sprintf('%s %s %s', $symfonyRootDir, nbConfig::get('symfony_project-deploy-init_site-user'), nbConfig::get('symfony_project-deploy-init_site-group'));
    try {
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    } catch (Exception $e) {
      $this->logLine('Cannot change permissions', nbLogger::ERROR);
    }
    
    // Create database
    if (nbConfig::has('mysql_create')) {
      $cmd = new nbMysqlCreateCommand();
      $cmdLine = sprintf('--config-file=%s', $configFilename);
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
      
    // Doctrine build
      $cmd = new nbSymfonyDoctrineBuildCommand();
      $cmdLine = sprintf('%s %s %s', $symfonyRootDir, nbConfig::get('symfony_project-deploy-init_environment'), '-f');
      $this->executeCommand($cmd, $cmdLine, $doit, $verbose);
    }
    
    // Clear cache
    $cmd = new nbSymfonyClearCacheCommand();
    $this->executeCommand($cmd, $symfonyRootDir, $doit, $verbose);

    $this->logLine('Symfony project init executed successfully');

    return true;
  }

  private function executeCommand(nbCommand $command, $commandLine, $doit, $verbose) {
    if ($doit) {
      $parser = new nbCommandLineParser();
      $parser->setDefaultConfigurationDirs($this->getParser()->getDefaultConfigurationDirs());

      if (!$command->run($parser, $commandLine))
        throw new Exception('Error executing: ' . $cmd);
    }

    if ($verbose)
      $this->logLine(sprintf("  <comment>Executing command: %s</comment>\n   %s\n", $command->getFullName(), $commandLine));
  }

}
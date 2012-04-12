<?php

class nbSymfony2DeployCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('Symfony2:deploy')
      ->setBriefDescription('Deploys a symfony 2 project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Deploying symfony2 project', nbLogger::COMMENT);

    if (!is_dir('./.bee') && !file_exists('./bee.yml')) {
      $message = 'No bee project defined!';
      $message .= "\n\n  Run: bee bee:generate-project";

      throw new InvalidArgumentException($message);
    }

    if (!isset($options['config-file']))
      throw new Exception('--config-file option required (CHANGE THIS)');

    $doit = isset($options['doit']);
    $verbose = isset($options['verbose']) || !$doit;

    // Load configuration
    $configDir = nbConfig::get('nb_plugins_dir') . '/nbSymfony2Plugin/config/';
    $configFilename = $options['config-file'];

    $this->loadConfiguration($configDir, $configFilename);

    // Setup config parameters
    $exePath = nbConfig::get('Symfony2_deploy_symfony-exe-path');
    $publicDir = nbConfig::get('Symfony2_deploy_public-dir');
    $webUser = nbConfig::get('web_user');
    $webGroup = nbConfig::get('web_group');
    $symfonyRootDir = nbConfig::get('Symfony2_deploy_symfony-root-dir');


    // Put site offline
    // TODO

    // Archive site directory
    $command = new nbArchiveDirCommand();
    $commandLine = sprintf('--config-file=%s --create-destination-dir', $configFilename);
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    // Dump database
    $command = new nbMysqlDumpCommand();
    $commandLine = '--config-file=' . $configFilename;
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    // Sync project
    $command = new nbDirTransferCommand();
    $commandLine = '--delete --config-file=' . $configFilename;
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    // Install assets
    $command = sprintf('php %s assets:install %s', $exePath, $publicDir);
    $this->executeShellCommand($command, $doit);

    // Clear cache
    $command = sprintf('php %s cache:clear --env=prod', $exePath);
    $this->executeShellCommand($command, $doit);

    // Change ownership
    $command = new nbChangeOwnershipCommand();
    $commandLine = sprintf('%s %s %s', $publicDir, $webUser, $webGroup);
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    $command = new nbChangeOwnershipCommand();
    $commandLine = sprintf('%s %s %s', $symfonyRootDir, $webUser, $webGroup);
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    // Check permissions
    $command = new nbMultiChangeModeCommand();
    $commandLine = sprintf('%s', $configFilename);
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    // Put site online
    // TODO

    $this->logLine('Done: Symfony2:deploy', nbLogger::COMMENT);
  }
}

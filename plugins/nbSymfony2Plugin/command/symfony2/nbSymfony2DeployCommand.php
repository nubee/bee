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

    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($pluginConfigFile);

    // Put site offline
    // TODO

    // Archive site directory
    $command = new nbArchiveDirCommand();
    $commandLine = sprintf('--config-file=%s --create-destination-dir', $configFilename);
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    // Dump database
    // TODO

    // Sync project
    $command = new nbDirTransferCommand();
    $commandLine = '--doit --delete --config-file=' . $configFilename;
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    // Install assets
    $command = sprintf('%s assets:install %s', nbConfig::get('Symfony2_deploy_symfony-exe-path'), nbConfig::get('Symfony2_deploy_public-dir'));
    $this->executeShellCommand($command, $doit);

    // Clear cache
    $command = sprintf('%s cache:clear --env=prod', nbConfig::get('Symfony2_deploy_symfony-exe-path'));
    $this->executeShellCommand($command, $doit);

    // Change ownership
    $command = new nbChangeOwnershipCommand();
    $commandLine = sprintf('%s %s %s --doit', nbConfig::get('Symfony2_deploy_public-dir'), nbConfig::get('web_user'), nbConfig::get('web_group'));
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    $command = new nbChangeOwnershipCommand();
    $commandLine = sprintf('%s %s %s --doit', nbConfig::get('Symfony2_deploy_symfony-root-dir'), nbConfig::get('web_user'), nbConfig::get('web_group'));
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    // Check permissions
    $command = new nbMultiChangeModeCommand();
    $commandLine = sprintf('%s --doit', $configFilename);
    $this->executeCommand($command, $commandLine, $doit, $verbose);

    // Put site online
    // TODO

    $this->logLine('Done: Symfony2:deploy', nbLogger::COMMENT);
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

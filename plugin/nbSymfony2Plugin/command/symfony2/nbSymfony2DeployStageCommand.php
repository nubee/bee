<?php

class nbSymfony2DeployStageCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony2:deploy-stage')
      ->setBriefDescription('Deploys a symfony 2 project in a stage environment')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('rebuild-db', 'r', nbOption::PARAMETER_NONE, 'Rebuilds the db: database:drop -> database:create -> schema:create'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Running: symfony2:deploy-stage', nbLogger::COMMENT);

    $pluginConfigFile = './.bee/nbSymfony2Plugin.yml';

    if (!file_exists($pluginConfigFile)) {
      $cmd = new nbConfigPluginCommand();
      $cmd->run(new nbCommandLineParser(), 'nbSymfony2Plugin');
      $this->logLine('Configuration file "' . $pluginConfigFile . '" was created.', nbLogger::INFO);
      $this->logLine('Modify it and re-run the command.', nbLogger::INFO);
      return true;
    }

    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($pluginConfigFile);

    if (nbConfig::get('symfony2_exec-sync')) {
      //sync
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\tsync project\n", nbLogger::INFO);
      if (nbConfig::has('filesystem_dir-transfer')) {
        $cmd = new nbDirTransferCommand();
        $commandLine = '--doit --delete --config-file=' . $pluginConfigFile;
        $cmd->run(new nbCommandLineParser(), $commandLine);
      }
    }

    $shell = new nbShell();

    if (isset($options['rebuild-db'])) {
      //rebuild database
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\trebuild database\n", nbLogger::INFO);
      
      $command = nbConfig::get('symfony2_bin') . ' doctrine:database:drop --force';
      if (!$shell->execute($command))
        $this->throwException($command);
      
      $command = nbConfig::get('symfony2_bin') . ' doctrine:database:create';
      if (!$shell->execute($command))
        $this->throwException($command);
      
      $command = nbConfig::get('symfony2_bin') . ' doctrine:schema:create';
      if (!$shell->execute($command))
        $this->throwException($command);
    } else {
      if (nbConfig::get('symfony2_exec-migrate')) {
        //migrate
        $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
        $this->logLine("\n\tmigrate\n", nbLogger::INFO);
        $command = nbConfig::get('symfony2_bin') . ' doctrine:migrations:migrate --no-interaction';

        if (!$shell->execute($command))
          $this->throwException($command);
      }
    }

    if (nbConfig::get('symfony2_exec-cache-clear')) {
      //clear cache
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\tclear cache\n", nbLogger::INFO);
      $command = nbConfig::get('symfony2_bin') . ' cache:clear';

      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('symfony2_exec-assets-install')) {
      //install assets
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\tinstall assets\n", nbLogger::INFO);
      $command = nbConfig::get('symfony2_bin') . ' assets:install ' . nbConfig::get('symfony2_web-dir');

      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('symfony2_exec-change-owner')) {
      //change owner
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\t change owner to cache, log and web dirs\n", nbLogger::INFO);
      $command = 'chown -R www-data:www-data '
        . nbConfig::get('symfony2_dir') . '/app/cache '
        . nbConfig::get('symfony2_dir') . '/app/logs '
        . nbConfig::get('symfony2_web-dir');

      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('symfony2_exec-change-mode')) {
      //change mode
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\t change mode to cache, log and web dirs\n", nbLogger::INFO);
      $command = 'chmod -R 755 '
        . nbConfig::get('symfony2_dir') . '/app/cache '
        . nbConfig::get('symfony2_dir') . '/app/logs '
        . nbConfig::get('symfony2_web-dir');

      if (!$shell->execute($command))
        $this->throwException($command);
    }

    $this->logLine('Done: symfony2:deploy-stage', nbLogger::COMMENT);
    return true;
  }

  private function throwException($command) {
    throw new LogicException(sprintf("
[nbSymfony2DeployStageCommand::execute] Error executing command:
  %s
", $command
    ));
  }

}
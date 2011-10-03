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
    if (!file_exists('./.bee/')) {
      $this->logLine('No bee project defined!', nbLogger::ERROR);
      $this->logLine('Run: <info>bee bee:generate-project</info>', nbLogger::COMMENT);
      return true;
    }

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

    if (nbConfig::get('stage_exec-sync')) {
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

      $command = nbConfig::get('stage_symfony2_bin') . ' doctrine:database:drop --force';
      if (!$shell->execute($command))
        $this->throwException($command);

      $command = nbConfig::get('stage_symfony2_bin') . ' doctrine:database:create';
      if (!$shell->execute($command))
        $this->throwException($command);

      $command = nbConfig::get('stage_symfony2_bin') . ' doctrine:schema:create';
      if (!$shell->execute($command))
        $this->throwException($command);

      if (nbConfig::get('stage_exec-migrate')) {
        $files = nbFileFinder::create('file')
          ->remove('.')
          ->remove('..')
          ->sortByName()
          ->in(nbConfig::get('stage_symfony2_migrations'));

        foreach ($files as $file) {
          preg_match('/Version(.+)\.php/s', $file, $version);
          $command = nbConfig::get('stage_symfony2_bin') . ' doctrine:migrations:version ' . $version[1] . ' --add';

          if (!$shell->execute($command))
            $this->throwException($command);
        }
      }
    } else {
      if (nbConfig::get('stage_exec-migrate')) {
        //migrate
        $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
        $this->logLine("\n\tmigrate\n", nbLogger::INFO);
        $command = nbConfig::get('stage_symfony2_bin') . ' doctrine:migrations:migrate --no-interaction';

        if (!$shell->execute($command))
          $this->throwException($command);
      }
    }

    if (nbConfig::get('stage_exec-cache-clear')) {
      //clear cache
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\tclear cache\n", nbLogger::INFO);
      $command = nbConfig::get('stage_symfony2_bin') . ' cache:clear';

      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('stage_exec-assets-install')) {
      //install assets
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\tinstall assets\n", nbLogger::INFO);
      $command = nbConfig::get('stage_symfony2_bin') . ' assets:install ' . nbConfig::get('stage_web-dir');

      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('stage_exec-change-owner')) {
      //change owner
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\t change owner to stage directory: " . nbConfig::get('stage_dir') . "\n", nbLogger::INFO);
      $command = 'chown -R www-data:www-data ' . nbConfig::get('stage_dir');

      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('stage_exec-change-mode')) {
      //change mode
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\t change mode to stage directory: " . nbConfig::get('stage_dir') . "\n", nbLogger::INFO);
      $command = 'chmod -R 555 ' . nbConfig::get('stage_dir');

      if (!$shell->execute($command))
        $this->throwException($command);

      $this->logLine("\n\t change mode to cache, logs directories\n", nbLogger::INFO);
      $command = 'chmod -R 755 '
        . nbConfig::get('stage_symfony2_cache') . ' '
        . nbConfig::get('stage_symfony2_logs');

      if (!$shell->execute($command))
        $this->throwException($command);

      if (nbConfig::has('stage_uploads-dir')) {
        $this->logLine("\n\t change mode to uploads directory\n", nbLogger::INFO);
        $command = 'chmod -R 755 ' . nbConfig::get('stage_uploads-dir');

        if (!$shell->execute($command))
          $this->throwException($command);
      }
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
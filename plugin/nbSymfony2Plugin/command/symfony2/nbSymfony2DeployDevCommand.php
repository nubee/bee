<?php

class nbSymfony2DeployDevCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony2:deploy-dev')
      ->setBriefDescription('Deploys a symfony 2 project in a dev environment')
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
      $this->logLine("\nNo bee project defined!", nbLogger::ERROR);
      $this->logLine("Run: <info>bee bee:generate-project</info>\n", nbLogger::COMMENT);
      return true;
    }

    $this->logLine("\n\nsymfony2:deploy-dev:\n\n\t<info>init</info>", nbLogger::COMMENT);

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

    $shell = new nbShell();

    if (isset($options['rebuild-db'])) {
      //rebuild database
      $this->logLine("\n\nsymfony2:deploy-dev:\n\n\t<info>rebuild database</info>", nbLogger::COMMENT);

      $command = nbConfig::get('dev_symfony2_bin') . ' doctrine:database:drop --force';
      if (!$shell->execute($command))
        $this->throwException($command);

      $command = nbConfig::get('dev_symfony2_bin') . ' doctrine:database:create';
      if (!$shell->execute($command))
        $this->throwException($command);

      $command = nbConfig::get('dev_symfony2_bin') . ' doctrine:schema:create';
      if (!$shell->execute($command))
        $this->throwException($command);

      if (nbConfig::get('dev_exec-migrate')) {
        $files = nbFileFinder::create('file')
          ->remove('.')
          ->remove('..')
          ->sortByName()
          ->in(nbConfig::get('dev_symfony2_migrations'));

        foreach ($files as $file) {
          preg_match('/Version(.+)\.php/s', $file, $version);
          $command = nbConfig::get('dev_symfony2_bin') . ' doctrine:migrations:version ' . $version[1] . ' --add';

          if (!$shell->execute($command))
            $this->throwException($command);
        }
      }
    } else {
      if (nbConfig::get('dev_exec-migrate')) {
        //migrate
        $this->logLine("\n\nsymfony2:deploy-dev:\n\n\t<info>migrate</info>", nbLogger::COMMENT);

        $command = nbConfig::get('dev_symfony2_bin') . ' doctrine:migrations:migrate --no-interaction';

        if (!$shell->execute($command))
          $this->throwException($command);
      }
    }

    if (nbConfig::get('dev_exec-cache-clear')) {
      //clear cache
      $this->logLine("\n\nsymfony2:deploy-dev:\n\n\t<info>clear cache</info>", nbLogger::COMMENT);

      $command = nbConfig::get('dev_symfony2_bin') . ' cache:clear';

      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('dev_exec-assets-install')) {
      //install assets
      $this->logLine("\n\nsymfony2:deploy-dev:\n\n\t<info>install assets</info>", nbLogger::COMMENT);

      $command = nbConfig::get('dev_symfony2_bin') . ' assets:install ' . nbConfig::get('dev_web-dir');

      if (!$shell->execute($command))
        $this->throwException($command);
    }

    $this->logLine("\n\nsymfony2:deploy-dev:\n\n\t<info>done</info>\n", nbLogger::COMMENT);
    return true;
  }

  private function throwException($command) {
    throw new LogicException(sprintf("
[nbSymfony2DeployDevCommand::execute] Error executing command:
  %s
", $command
    ));
  }

}
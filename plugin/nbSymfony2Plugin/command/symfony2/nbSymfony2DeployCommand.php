<?php

class nbSymfony2DeployCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('symfony2:deploy')
      ->setBriefDescription('Deploys a symfony 2 project in a specified environment')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('environment', nbArgument::REQUIRED, 'Environment name ( dev | prod )'),
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
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

    $doit = isset($options['doit']);
    $env = $arguments['environment'];
    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($pluginConfigFile);

    if (nbConfig::get($env . '_exec_sync')) {
      //sync
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\tsync project\n", nbLogger::INFO);

      //sync httpdocs
      $command = new nbDirTransferCommand();
      $commandLine = nbConfig::get($env . '_source_web_dir') . ' '
        . nbConfig::get($env . '_www_web_dir') . ($doit ? ' --doit' : '')
        . ' --delete --exclude-from="./.bee/excludes"';
      $command->run(new nbCommandLineParser(), $commandLine);

      //sync symfony
      $command = new nbDirTransferCommand();
      $commandLine = nbConfig::get($env . '_source_symfony_dir') . ' '
        . nbConfig::get($env . '_www_symfony_dir') . ($doit ? ' --doit' : '')
        . ' --delete --exclude-from="./.bee/excludes"';
      $command->run(new nbCommandLineParser(), $commandLine);
    }

    if (isset($options['rebuild-db'])) {
      //rebuild database
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\trebuild database\n", nbLogger::INFO);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:database:drop --force --env=' . $env;
      $this->executeCommandLine($command, $doit);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:database:create --env=' . $env;
      $this->executeCommandLine($command, $doit);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:schema:create --env=' . $env;
      $this->executeCommandLine($command, $doit);

      if (nbConfig::get($env . '_exec_migrate')) {
        $files = nbFileFinder::create('file')
          ->remove('.')
          ->remove('..')
          ->sortByName()
          ->in(nbConfig::get($env . '_www_symfony_migrations_dir'));

        foreach ($files as $file) {
          preg_match('/Version(.+)\.php/s', $file, $version);

          $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:migrations:version ' . $version[1] . '  --env=' . $env . ' --add';
          $this->executeCommandLine($command, $doit);
        }
      }
    } else {
      if (nbConfig::get($env . '_exec_migrate')) {
        //migrate
        $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
        $this->logLine("\n\tmigrate\n", nbLogger::INFO);

        $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:migrations:migrate --env=' . $env . ' --no-interaction';
        $this->executeCommandLine($command, $doit);
      }
    }

    if (nbConfig::get($env . '_exec_cache_clear')) {
      //clear cache
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\tclear cache\n", nbLogger::INFO);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' cache:clear --env=' . $env;
      $this->executeCommandLine($command, $doit);
    }

    if (nbConfig::get($env . '_exec_assets_install')) {
      //install assets
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      $this->logLine("\n\tinstall assets\n", nbLogger::INFO);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' assets:install ' . nbConfig::get($env . '_www_web_dir');
      $this->executeCommandLine($command, $doit);
    }

    if (nbConfig::get($env . '_exec_change_owner')) {
      //change owner
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);

      $this->logLine("\n\t<comment>change owner to directory:</comment> " . nbConfig::get($env . '_www_web_dir') . "\n", nbLogger::INFO);
      $command = 'chown -R ' . nbConfig::get($env . '_owner') . ':' . nbConfig::get($env . '_group') . ' ' . nbConfig::get($env . '_www_web_dir');
      $this->executeCommandLine($command, $doit);

      $this->logLine("\n\t<comment>change owner to directory:</comment> " . nbConfig::get($env . '_www_symfony_dir') . "\n", nbLogger::INFO);
      $command = 'chown -R ' . nbConfig::get($env . '_owner') . ':' . nbConfig::get($env . '_group') . ' ' . nbConfig::get($env . '_www_symfony_dir');
      $this->executeCommandLine($command, $doit);
    }

    if (nbConfig::get($env . '_exec_change_mode')) {
      //change mode
      $this->logLine('symfony2:deploy-stage', nbLogger::COMMENT);
      
      //change mode web dir 555
      $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_web_dir') . "\n", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_web_dir') . ' -type d -exec chmod 555 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode web dir (files) 444
      $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_web_dir') . " (files)\n", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_web_dir') . ' -type f -exec chmod 444 {} \\; ';
      $this->executeCommandLine($command, $doit);

      if (nbConfig::has($env . '_www_uploads_dir')) {
        //change mode uploads dir 755
        $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_uploads_dir') . "\n", nbLogger::INFO);
        $command = 'find ' . nbConfig::get($env . '_www_uploads_dir') . ' -type d -exec chmod 755 {} \\; ';
        $this->executeCommandLine($command, $doit);

        //change mode uploads dir (files) 644
        $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_uploads_dir') . " (files)\n", nbLogger::INFO);
        $command = 'find ' . nbConfig::get($env . '_www_uploads_dir') . ' -type f -exec chmod 644 {} \\; ';
        $this->executeCommandLine($command, $doit);
      }

      //change mode symfony 555
      $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_dir') . "\n", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_dir') . ' -type d -exec chmod 555 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode symfony (files) 444
      $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_dir') . " (files)\n", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_dir') . ' -type f -exec chmod 444 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode cache 755
      $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_cache_dir') . "\n", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_cache_dir') . ' -type d -exec chmod 755 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode cache (files) 644
      $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_cache_dir') . " (files)\n", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_cache_dir') . ' -type f -exec chmod 644 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode logs 755
      $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_logs_dir') . "\n", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_logs_dir') . ' -type d -exec chmod 755 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode logs (files) 644
      $this->logLine("\n\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_logs_dir') . " (files)\n", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_logs_dir') . ' -type f -exec chmod 644 {} \\; ';
      $this->executeCommandLine($command, $doit);
    }

    $this->logLine('Done: symfony2:deploy-stage', nbLogger::COMMENT);
    return true;
  }

  private function executeCommandLine($command, $doit = false) {
    if ($doit) {
      $shell = new nbShell();
      if (!$shell->execute($command))
        throw new LogicException(sprintf("
[nbSymfony2DeployStageCommand::execute] Error executing command:
  %s
", $command
        ));
    } else {
      $this->logLine("\t<comment>command to execute:</comment> " . $command . "\n");
    }
  }

}

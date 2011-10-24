<?php

class nbSymfony2DeployCommand extends nbApplicationCommand
{

  protected function configure()
  {
    $this->setName('symfony2:deploy')
      ->setBriefDescription('Deploys a symfony 2 project in a specified environment')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('environment', nbArgument::REQUIRED, 'Environment name ( stage | prod )'),
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
        new nbOption('rebuild-db', 'r', nbOption::PARAMETER_NONE, 'Rebuilds the db: database:drop -> database:create -> schema:create'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    if(!file_exists('./.bee/')) {
      $this->logLine('No bee project defined!', nbLogger::ERROR);
      $this->logLine('Run: <info>bee bee:generate-project</info>', nbLogger::COMMENT);
      return true;
    }

    $this->logLine('Running: symfony2:deploy', nbLogger::COMMENT);

    $pluginConfigFile = './.bee/nbSymfony2Plugin.yml';

    if(!file_exists($pluginConfigFile)) {
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

    if(nbConfig::get($env . '_exec_sync')) {
      //sync
      $this->logLine('symfony2:deploy', nbLogger::COMMENT);
      $this->logLine("\tsync project", nbLogger::INFO);

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

    if(isset($options['rebuild-db'])) {
      //rebuild database
      $this->logLine('symfony2:deploy', nbLogger::COMMENT);
      $this->logLine("\trebuild database", nbLogger::INFO);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:database:drop --force --env=' . nbConfig::get($env . '_environment');
      $this->executeCommandLine($command, $doit);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:database:create --env=' . nbConfig::get($env . '_environment');
      $this->executeCommandLine($command, $doit);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:schema:create --env=' . nbConfig::get($env . '_environment');
      $this->executeCommandLine($command, $doit);

      if(nbConfig::get($env . '_exec_migrate')) {
        $files = nbFileFinder::create('file')
          ->remove('.')
          ->remove('..')
          ->sortByName()
          ->in(nbConfig::get($env . '_www_symfony_migrations_dir'));

        foreach($files as $file) {
          preg_match('/Version(.+)\.php/s', $file, $version);

          $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:migrations:version ' . $version[1] . '  --env=' . nbConfig::get($env . '_environment') . ' --add';
          $this->executeCommandLine($command, $doit);
        }
      }
    }
    else {
      if(nbConfig::get($env . '_exec_migrate')) {
        //migrate
        $this->logLine('symfony2:deploy', nbLogger::COMMENT);
        $this->logLine("\tmigrate", nbLogger::INFO);

        $command = nbConfig::get($env . '_www_symfony_bin') . ' doctrine:migrations:migrate --env=' . nbConfig::get($env . '_environment') . ' --no-interaction';
        $this->executeCommandLine($command, $doit);
      }
    }

    if(nbConfig::get($env . '_exec_cache_clear')) {
      //clear cache
      $this->logLine('symfony2:deploy', nbLogger::COMMENT);
      $this->logLine("\tclear cache", nbLogger::INFO);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' cache:clear --env=' . nbConfig::get($env . '_environment');
      $this->executeCommandLine($command, $doit);
    }

    if(nbConfig::get($env . '_exec_assets_install')) {
      //install assets
      $this->logLine('symfony2:deploy', nbLogger::COMMENT);
      $this->logLine("\tinstall assets", nbLogger::INFO);

      $command = nbConfig::get($env . '_www_symfony_bin') . ' assets:install ' . nbConfig::get($env . '_www_web_dir');
      $this->executeCommandLine($command, $doit);
    }

    if(nbConfig::get($env . '_exec_change_owner')) {
      //change owner
      $this->logLine('symfony2:deploy', nbLogger::COMMENT);

      $this->logLine("\t<comment>change owner to directory:</comment> " . nbConfig::get($env . '_www_web_dir') . "", nbLogger::INFO);
      $command = 'chown -R ' . nbConfig::get($env . '_owner') . ':' . nbConfig::get($env . '_group') . ' ' . nbConfig::get($env . '_www_web_dir');
      $this->executeCommandLine($command, $doit);
      
//      $command = new nbChangeOwnershipCommand();
//      $commandLine = sprintf('%s %s %s',
//        nbConfig::get($env . '_www_web_dir'),
//        nbConfig::get($env . '_owner'),
//        nbConfig::get($env . '_group'),
//        $doit);
//      $command->run(new nbCommandLineParser(), $commandLine);
      
      
      
      

      $this->logLine("\t<comment>change owner to directory:</comment> " . nbConfig::get($env . '_www_symfony_dir') . "", nbLogger::INFO);
      $command = 'chown -R ' . nbConfig::get($env . '_owner') . ':' . nbConfig::get($env . '_group') . ' ' . nbConfig::get($env . '_www_symfony_dir');
      $this->executeCommandLine($command, $doit);
    }

    if(nbConfig::get($env . '_exec_change_mode')) {
      //change mode
      $this->logLine('symfony2:deploy', nbLogger::COMMENT);

      //change mode web dir 555
      $this->logLine("\t<comment>Changing mode to directory:</comment> " . nbConfig::get($env . '_www_web_dir') . "", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_web_dir') . ' -type d -exec chmod 555 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode web dir (files) 444
      $this->logLine("\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_web_dir') . " (files)", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_web_dir') . ' -type f -exec chmod 444 {} \\; ';
      $this->executeCommandLine($command, $doit);

      if(nbConfig::has($env . '_www_uploads_dir')) {
        //change mode uploads dir 755
        $this->logLine("\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_uploads_dir') . "", nbLogger::INFO);
        $command = 'find ' . nbConfig::get($env . '_www_uploads_dir') . ' -type d -exec chmod 755 {} \\; ';
        $this->executeCommandLine($command, $doit);

        //change mode uploads dir (files) 644
        $this->logLine("\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_uploads_dir') . " (files)", nbLogger::INFO);
        $command = 'find ' . nbConfig::get($env . '_www_uploads_dir') . ' -type f -exec chmod 644 {} \\; ';
        $this->executeCommandLine($command, $doit);
      }

      //change mode symfony 555
      $this->logLine("\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_dir') . "", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_dir') . ' -type d -exec chmod 555 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode symfony (files) 444
      $this->logLine("\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_dir') . " (files)", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_dir') . ' -type f -exec chmod 444 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode cache 755
      $this->logLine("\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_cache_dir') . "", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_cache_dir') . ' -type d -exec chmod 755 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode cache (files) 644
      $this->logLine("\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_cache_dir') . " (files)", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_cache_dir') . ' -type f -exec chmod 644 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode logs 755
      $this->logLine("\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_logs_dir') . "", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_logs_dir') . ' -type d -exec chmod 755 {} \\; ';
      $this->executeCommandLine($command, $doit);

      //change mode logs (files) 644
      $this->logLine("\t<comment>change mode to directory:</comment> " . nbConfig::get($env . '_www_symfony_logs_dir') . " (files)", nbLogger::INFO);
      $command = 'find ' . nbConfig::get($env . '_www_symfony_logs_dir') . ' -type f -exec chmod 644 {} \\; ';
      $this->executeCommandLine($command, $doit);
    }

    $this->logLine('Done: symfony2:deploy', nbLogger::COMMENT);
  }

  private function executeCommandLine($command, $doit = false)
  {
    if($doit)
      $this->executeShellCommand($command);
    else
      $this->logLine("\t<comment>Executing: </comment> " . $command . "");
  }

}

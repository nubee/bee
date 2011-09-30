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
        new nbArgument('config-file', nbArgument::REQUIRED, 'Deploy configuration file')
      )));

    $this->setOptions(new nbOptionSet(array(
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Running: symfony2:deploy-stage', nbLogger::COMMENT);
    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($arguments['config-file']);

    if (nbConfig::get('symfony2_exec-sync')) {
      //sync
      $this->logLine("symfony2:deploy-stage\n\tsync project", nbLogger::COMMENT);
      if (nbConfig::has('filesystem_dir-transfer')) {
        $cmd = new nbDirTransferCommand();
        $commandLine = '--doit --delete --config-file=' . $arguments['config-file'];
        $cmd->run(new nbCommandLineParser(), $commandLine);
      }
    }

    $shell = new nbShell();

    if (nbConfig::get('symfony2_exec-migrate')) {
      //migrate
      $this->logLine("symfony2:deploy-stage\n\tapply migrations", nbLogger::COMMENT);
      $command = nbConfig::get('symfony2_bin') . ' doctrine:migrations:migrate --no-interaction';
      
      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('symfony2_exec-cache-clear')) {
      //clear cache
      $this->logLine("symfony2:deploy-stage\n\tclear cache", nbLogger::COMMENT);
      $command = nbConfig::get('symfony2_bin') . ' cache:clear';
      
      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('symfony2_exec-change-owner')) {
      //change owner
      $this->logLine("symfony2:deploy-stage\n\tchange owner to 'cache' and 'logs' dirs", nbLogger::COMMENT);
      $command = 'chown -R www-data:www-data ' . nbConfig::get('symfony2_dir') . '/app/cache ' . nbConfig::get('symfony2_dir') . '/app/logs';
      
      if (!$shell->execute($command))
        $this->throwException($command);
    }
    
    if (nbConfig::get('symfony2_exec-change-mode')) {
      //change mode
      $this->logLine("symfony2:deploy-stage\n\tchange mode to 'cache' and 'logs' dirs", nbLogger::COMMENT);
      $command = 'chmod -R 777 ' . nbConfig::get('symfony2_dir') . '/app/cache ' . nbConfig::get('symfony2_dir') . '/app/logs';
      
      if (!$shell->execute($command))
        $this->throwException($command);
    }

    if (nbConfig::get('symfony2_exec-assets-install')) {
      //install assets
      $this->logLine("symfony2:deploy-stage\n\tinstall assets", nbLogger::COMMENT);
      $command = nbConfig::get('symfony2_bin') . ' assets:install ' . nbConfig::get('symfony2_web-dir');
      echo $command;
      die;
      
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
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
    $this->logLine('Running: symfony2:deploy-stage');
    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($arguments['config-file']);

    //sync project
    if (nbConfig::has('filesystem_dir-transfer')) {
      $cmd = new nbDirTransferCommand();
      $commandLine = '--doit --delete --config-file=' . $arguments['config-file'];
      $cmd->run(new nbCommandLineParser(), $commandLine);
    }
    
    $shell = new nbShell();

    //migrate
    $command = nbConfig::get('symfony2_bin').' doctrine:migrations:migrate --no-interaction';
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbSymfony2DeployStageCommand::execute] Error executing command:
  %s
",
        $command
      ));
    }
    
    //clear cache
    $command = nbConfig::get('symfony2_bin').' cache:clear';
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbSymfony2DeployStageCommand::execute] Error executing command:
  %s
",
        $command
      ));
    }

    $this->logLine('Done: symfony2:deploy-stage');
    return true;
  }

}
<?php

class nbVsBuildCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('vs:build')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('vsproject', nbArgument::REQUIRED, 'VS project file')
      )))
      ->setBriefDescription('Builds a Visual C++ project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command builds a Visual C++ project:

    <info>./bee {$this->getFullName()} vsproject</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    echo "\nUnder construction...\nSee the code in nbVsBuildCommand\n"; die;

    $vsproj = new nbVcProjParser($arguments['vsproject'], nbVcProjParser::DEBUG);
    $shell = new nbShell();
    $client = new nbVisualStudioClient(
      $vsproj->getName(),
      nbVisualStudioClient::APP, //TODO: get from nbConfig|nbVcProjParser
      nbVisualStudioClient::DEBUG //TODO: get from command line
    );
    //TODO: unify nbVisualStudioClient constants with nbVcProjParser constants
    $vsproj->getBinDir();
    $vsproj->getObjDir();
    $client->setProjectDefines($vsproj->getDefines());
    $client->setProjectIncludes($vsproj->getIncludes());
    $client->setProjectSources($vsproj->getSources());
    $client->setProjectLibPaths($vsproj->getLibPaths());
    $client->setProjectLibs($vsproj->getLibs());

    $this->log('Compiling ', nbLogger::COMMENT);
    $this->log(nbConfig::get('proj_name'));
    $this->log("\n");
    $command = $client->getCompilerCmdLine();

    echo $command . "\n";

    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbBuildCommand::execute] Error executing command:
  %s
",
        $command
      ));
    }

    $this->log('Linking ', nbLogger::COMMENT);
    $this->log(nbConfig::get('proj_name'));
    $this->log("\n");
    $command = $client->getLinkerCmdLine();

    echo $command . "\n";
    
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbBuildCommand::execute] Error executing command:
  %s
",
        $command
      ));
    }
  }
}

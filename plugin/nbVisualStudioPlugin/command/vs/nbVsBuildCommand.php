<?php

class nbVsBuildCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('vs:build')
      ->setBriefDescription('Builds a Visual C++ project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command builds a Visual C++ project:

    <info>./bee {$this->getFullName()}</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    echo "\nUnder construction...\n"; die;
    $yaml = new nbYamlConfigParser();
    $yaml->parseFile('project.yml');

    $shell = new nbShell();
    $client = new nbVisualStudioClient(
      nbConfig::get('proj_name'),
      nbVisualStudioClient::APP, //TODO: get from nbConfig
      nbVisualStudioClient::DEBUG //TODO: get from nbConfig
    );


    $client->setProjectDefines(nbConfig::get('proj_define'));
    $finder = nbFileFinder::create('dir');
    $includes = array();
    $includes = array_merge($includes, nbConfig::get('proj_include'));
    $includes = array_merge($includes, $finder->add('include')->in(nbConfig::get('proj_libdir')));
    $includes = array_merge($includes, $finder->add('include')->in(nbConfig::get('proj_dir')));
    foreach ($includes as $k => $v)
//      $includes[$k] = str_replace('/', '\\', trim($v, '\\'));
      $includes[$k] = trim($v, '\\');
    $client->setProjectIncludes($includes);

    $finder->setType('file');
    $sources = $finder->add('*.cpp')->in(nbConfig::get('proj_dir'));
//    foreach ($sources as $k => $v)
//      $sources[$k] = str_replace('/', '\\', $v);
    $client->setProjectSources($sources);

    $this->log('Compiling ', nbLogger::COMMENT);
    $this->log(nbConfig::get('proj_name'));
    $this->log("\n");
    $command = $client->getCompilerCmdLine();

    echo $command . "\n"; die;

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
//    echo $command . "\n";
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

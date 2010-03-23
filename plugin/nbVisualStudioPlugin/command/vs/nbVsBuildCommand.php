<?php

class nbVsBuildCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('vs:build')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('project', nbArgument::REQUIRED, 'Visual Studio project file'),
        new nbArgument('configuration', nbArgument::REQUIRED, 'Target configuration to build')
      )))
      ->setOptions(new nbOptionSet(array(
        new nbOption('incremental', 'i', nbOption::PARAMETER_NONE, 'Make an incremental build')
      )))
      ->setBriefDescription('Builds a Visual C++ project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command builds a Visual C++ project:

    <info>./bee {$this->getFullName()} project</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Building project ', nbLogger::COMMENT);
    $this->log($arguments['project']);
    $this->log("\n");
    $this->log('Target configuration ', nbLogger::COMMENT);
    $this->log($arguments['configuration']);
    $this->log("\n");

    $command = 'vcbuild /nondefmsbuild /nologo ';
    if(!isset($options['incremental']))
      $command .= '/rebuild ';
    $command .= '"' . $arguments['project'] . '" "' . $arguments['configuration'] . '"';

    $shell = new nbShell();
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbVsBuildCommand::execute] Error executing command:
  %s
  project       -> %s
  configuration -> %s
  incremental   -> %s
",
        $command, $arguments['project'], $arguments['configuration'], $options['incremental']
      ));
    }
  }
}

<?php

class nbVsBuildCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('vs:build')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('configuration', nbArgument::OPTIONAL, 'Target configuration to build', 'Debug')
      )))
      ->setOptions(new nbOptionSet(array(
        new nbOption('test', '', nbOption::PARAMETER_NONE, 'Builds project tests'),
//        new nbOption('configuration', 'c', nbOption::PARAMETER_REQUIRED, 'Target configuration to build'),
        new nbOption('incremental', 'i', nbOption::PARAMETER_NONE, 'Make an incremental build')
      )))
      ->setBriefDescription('Builds a Visual C++ project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command builds a Visual C++ project:

    <info>./bee {$this->getFullName()}</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $project = isset($options['test']) ? nbConfig::get('project_test') : nbConfig::get('project_core');
    $configuration = $arguments['configuration'];
    $incremental = isset($options['incremental']) ? '/rebuild' : '';

    $this->logLine(sprintf('Building project "%s" (target: %s'), $project, $configuration);

    $info = "\033[32m[info]: \033[0m";
    $warning = "\033[33m[warning]: \033[0m";
    $error = "\033[31m[error]: \033[0m";

    $command = sprintf('vcbuild /nondefmsbuild /nologo /info:"%s" /warning:"%s" /error:"%s" %s "%s" "%s"', $info, $warning, $error, $incremental, $project, $configuration);

    $this->executeShellCommand($command);
  }
}

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
    $project = isset($options['test']) ? nbConfig::get('proj_test') : nbConfig::get('proj_core');
//    $configuration = isset($options['configuration']) ? $options['configuration'] : nbConfig::get('nb_commands_vs_build_configuration');
    $configuration = $arguments['configuration'];

    $this->log('Building project ', nbLogger::COMMENT);
    $this->log($project);
    $this->log("\n");
    $this->log('Target configuration ', nbLogger::COMMENT);
    $this->log($configuration);
    $this->log("\n");

    $info = "\033[32m[info]: \033[0m";
    $warning = "\033[33m[warning]: \033[0m";
    $error = "\033[31m[error]: \033[0m";

    $command = "vcbuild /nondefmsbuild /nologo /info:\"" . $info . "\" /warning:\"" . $warning . "\" /error:\"" . $error . "\" ";
    if(!isset($options['incremental']))
      $command .= '/rebuild ';
    $command .= '"' . $project . '" "' . $configuration . '"';

    $shell = new nbShell();
    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbVsBuildCommand::execute] Error executing command:
  %s
  configuration -> %s
  test          -> %s
  incremental   -> %s
",
        $command, @$arguments['configuration'], @$options['test'], @$options['incremental']
      ));
    }
  }
}

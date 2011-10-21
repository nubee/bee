<?php

class nbGtestTestCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('gtest:test')
      ->setArguments(new nbArgumentSet(array(
        new nbArgument('testapp', nbArgument::OPTIONAL, 'Test application', nbConfig::get('project_testapp'))
      )))
      ->setOptions(new nbOptionSet(array(
//        new nbOption('list', '', nbOption::PARAMETER_NONE, 'Lists tests'),
        new nbOption('output', '', nbOption::PARAMETER_OPTIONAL, 'Puts test results into a xml file', 'test-result.xml'),
//        new nbOption('nocolor', '', nbOption::PARAMETER_NONE, 'Disables colors')
      )))
      ->setBriefDescription('Runs gtest tests')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> run gtest tests:

    <info>./bee {$this->getFullName()}</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $shell = new nbShell();
    $this->log('Running tests', nbLogger::COMMENT);
    $this->log("\n");

    $command = '"' . $arguments['testapp'] . '" ';
    if(isset($options['output'])) {
      $command .= '--gtest_output=xml:' . $options['output'] . ' ';
      $testResultDir = dirname(nbConfig::get('project_testresult'));
      nbFileSystem::rmdir($testResultDir, true);
      nbFileSystem::mkdir($testResultDir, true);
    }
//    if(isset($options['nocolor']))
//      $command .= '--gtest_color=no ';

    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbGtestTestCommand::execute] Error executing command:
  %s
  testapp: %s
  output:  %s
",
        $command, @$arguments['testapp'], @$options['output']
      ));
    }
  }
}

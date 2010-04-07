<?php

/**
 * Launches unit tests.
 *
 * @package    bee
 * @subpackage command
 */
class nbTestPluginsCommand extends nbApplicationCommand
{
  protected function configure()
  {
    $this->setName('bee:test-plugin')
      ->setBriefDescription('Run unit tests for bee plugins ')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command
TXT
    );
    
    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('name', nbArgument::OPTIONAL | nbArgument::IS_ARRAY, 'The test name')
    )));

    $this->setOptions(new nbOptionSet(array(
      new nbOption('output', 'o', nbOption::PARAMETER_REQUIRED, 'Outputs to filename'),
      new nbOption('showall', '', nbOption::PARAMETER_NONE, 'Show all tests one by one'),
      new nbOption('xml', 'x', nbOption::PARAMETER_NONE, 'Outputs in xml format'),
    )));
  }
  
  protected function execute(array $arguments = array(), array $options = array())
  {
        $commandSet = $this->getApplication()->getCommands();
        $testCmd = $commandSet->getCommand('lime:test');

        $pluginTestDirs = nbConfig::get('nb_pugin_test_dirs');

        $options['dir'] = $pluginTestDirs;
        $options['exclude-project-folder'] = true;
        $testCmd->execute($arguments,$options);
  }
}

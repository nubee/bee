<?php

/**
 * Launches unit tests.
 *
 * @package    bee
 * @subpackage command
 */
class nbTestUnitCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('test:unit')
      ->setBriefDescription('Run unit tests')
      ->setDescription(<<<TXT
The <info>test:unit</info> command
TXT
        );
    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('name', nbArgument::OPTIONAL | nbArgument::IS_ARRAY, 'The test name'),
    )));

    $this->setOptions(new nbOptionSet(array(
      new nbOption('xml', 'x', nbOption::PARAMETER_NONE, 'Outputs in xml format'),
      new nbOption('filename', 'f', nbOption::PARAMETER_REQUIRED, 'Outputs to filename'),
    )));
  }
  
  protected function execute(array $arguments = array(), array $options = array())
  {
    if(count($arguments['name'])) {
      $files = array();

      foreach($arguments['name'] as $name) {
        $finder = nbFileFinder::create('file')->followLink()->add(basename($name) . 'Test.php');
        $files = array_merge($files, $finder->in(nbConfig::get('nb_test_dir').dirname($name)));
      }

      if(count($files) > 0) {
        foreach ($files as $file)
          include($file);
      }
      else
        $this->log('no tests found', nbLogger::ERROR);
    }
    else {
      $h = new lime_harness();

      // filter and register unit tests
      $finder = nbFileFinder::create('file')->add('*Test.php');
      $h->register($finder->in(nbConfig::get('nb_test_dir', 'test/unit')));

      $ret = $h->run() ? 0 : 1;

      return $ret;
    }
  }
}
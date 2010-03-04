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
    $h = new lime_harness();

    if(count($arguments['name'])) {
      $files = array();

      foreach($arguments['name'] as $name) {
        $finder = nbFileFinder::create('file')->followLink()->add(basename($name) . 'Test.php');
        $files = array_merge($files, $finder->in(nbConfig::get('nb_test_dir').dirname($name)));
      }

      if(count($files) > 0) {
        foreach ($files as $file)
          $h->register($file); 
          //include($file);
      }
      else
        $this->log('no tests found', nbLogger::ERROR);
    }
    else {
      // filter and register unit tests
      $finder = nbFileFinder::create('file')->add('*Test.php');
      $h->register($finder->in(nbConfig::get('nb_test_dir', 'test/unit')));
    }
    $ret = $h->run();

    // print output to file
    if (isset($options['filename'])) {
      $fileName = $options['filename'];
      $fh = fopen($fileName, 'w');
      if ($fh === false)
        return $ret;
      fwrite($fh, isset($options['xml']) ? $h->to_xml() : '');
      fclose($fh);
    }

    return $ret;
  }
}

<?php

/**
 * Launches unit tests.
 *
 * @package    bee
 * @subpackage command
 */
class nbLimeTestCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('lime:test')
      ->setBriefDescription('Run lime unit tests')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command runs unit tests using lime.
TXT
    );
    
    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('name', nbArgument::OPTIONAL | nbArgument::IS_ARRAY, 'The test name'),
    )));

    $this->setOptions(new nbOptionSet(array(
      new nbOption('dir', 'd', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY, 'Load tests from dir'),
      new nbOption('output', 'o', nbOption::PARAMETER_REQUIRED, 'Outputs to filename'),
      new nbOption('showall', '', nbOption::PARAMETER_NONE, 'Show all tests one by one'),
      new nbOption('xml', 'x', nbOption::PARAMETER_NONE, 'Outputs in xml format'),
    )));
  }
  
  protected function execute(array $arguments = array(), array $options = array())
  {
    $files = array();
    $dirs = isset($options['dir'])?$options['dir']:array();
    $dirs[] = nbConfig::get('nb_test_dir', 'test/unit');

    if(count($arguments['name'])) {
      foreach($dirs as $dir)
        foreach($arguments['name'] as $name) {
          $finder = nbFileFinder::create('file')->followLink()->add(basename($name) . 'Test.php');
          $files = array_merge($files, $finder->in($dir . '/' . dirname($name)));
        }
    }
    else {
      // filter and register unit tests
      $finder = nbFileFinder::create('file')->add('*Test.php');
      $files = $finder->in($dirs);
    }
    
    if(count($files) == 0) {
      $this->log('no tests found', nbLogger::ERROR);
      return false;
    }

//    if(count(isset($options['showall'])) {
//      foreach($files as $file)
//        include($file);
//    }

    $h = new lime_harness();
    $h->register($files); 

    $ret = $h->run(isset($options['showall']));

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

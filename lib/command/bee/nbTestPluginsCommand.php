<?php

/**
 * Launches unit tests.
 *
 * @package    bee
 * @subpackage command
 */
class nbTestPluginsCommand extends nbCommand
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
      new nbArgument('plugin', nbArgument::OPTIONAL | nbArgument::IS_ARRAY, 'The plugin name, use pluginName/testName to run a single test of a single plugin')
    )));

    $this->setOptions(new nbOptionSet(array(
      new nbOption('filename', 'f', nbOption::PARAMETER_REQUIRED, 'Outputs to filename'),
      new nbOption('showall', '', nbOption::PARAMETER_NONE, 'Show all tests one by one'),
      new nbOption('xml', 'x', nbOption::PARAMETER_NONE, 'Outputs in xml format'),
    )));
  }
  
  protected function execute(array $arguments = array(), array $options = array())
  {
    $files = array();
    $pluginLoader = nbPluginLoader::getInstance();
    $finder = nbFileFinder::create('file')->followLink()->add('*Test.php');
    $files = array();

    if(! count($arguments['plugin'])) {
      $pluginLoader->loadAllPlugins();
      foreach($pluginLoader->getPluginDirs() as $name=>$dir) {
       $files = array_merge($files,$finder->in($dir . '/test/unit/'));
      }
    }
    else {
      $pluginLoader->loadPlugins($arguments['plugin']);
      $pluginDirs = $pluginLoader->getPluginDirs();
      foreach( $arguments['plugin'] as $name) {
        if(key_exists($name, $pluginDirs))
          $files = array_merge($files,$finder->in($pluginDirs[$name] . '/test/unit/'));
      }
    }

    if(count($files) == 0) {
      $this->log('no tests found', nbLogger::ERROR);
      return false;
    }

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

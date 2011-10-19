<?php

class nbGeneratePluginCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('bee:generate-plugin')
      ->setBriefDescription('Generates the directory structure for a new plugin ')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

   <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('plugin_name', nbArgument::REQUIRED, 'The plugin name')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'Overwrites the existing plugin'),
        new nbOption('directory', 'd', nbOption::PARAMETER_REQUIRED, 'Creates plugin directory structure inside directory')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $pluginName = $arguments['plugin_name'];

    $targetDir = (isset($options['directory']) ? $options['directory'] : nbConfig::get('nb_plugin_dir'));

    $pluginDir = $targetDir . '/' . $pluginName;

    $fs = $this->getFileSystem();
    
    if(isset($options['force']))
      $fs->rmdir($pluginDir);
    
    $fs->mkdir($pluginDir, true);
    $fs->mkdir($pluginDir . '/command');
    $fs->mkdir($pluginDir . '/config');
    $fs->mkdir($pluginDir . '/lib');
    $fs->mkdir($pluginDir . '/test');
    $fs->mkdir($pluginDir . '/test/config');
    $fs->mkdir($pluginDir . '/test/data');
    $fs->mkdir($pluginDir . '/test/unit');
    $fs->mkdir($pluginDir . '/vendor');
  }

}
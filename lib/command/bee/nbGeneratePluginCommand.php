<?php

class nbGeneratePluginCommand  extends nbCommand
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

    if(isset ($options['directory']))
      $targetDir = $options['directory'];
    else
      $targetDir = nbConfig::get('nb_plugin_dir');

    $targetPluginDir = $targetDir.'/'.$pluginName;

    if(isset ($options['force']))
        $force = true;
    else
        $force = false;    

    nbFileSystemUtils::mkdir($targetPluginDir,$force);
    nbFileSystemUtils::mkdir($targetPluginDir.'/command');
    nbFileSystemUtils::mkdir($targetPluginDir.'/config');
    nbFileSystemUtils::mkdir($targetPluginDir.'/lib');
    nbFileSystemUtils::mkdir($targetPluginDir.'/test');
    nbFileSystemUtils::mkdir($targetPluginDir.'/test/config');
    nbFileSystemUtils::mkdir($targetPluginDir.'/test/data');
    nbFileSystemUtils::mkdir($targetPluginDir.'/test/unit');
    nbFileSystemUtils::mkdir($targetPluginDir.'/vendor');
  }

}
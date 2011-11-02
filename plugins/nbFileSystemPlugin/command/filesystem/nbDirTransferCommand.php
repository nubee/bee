<?php

class nbDirTransferCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('filesystem:dir-transfer')
      ->setBriefDescription('Rsyncs a directory with another local directory')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('source-dir', nbArgument::REQUIRED, 'Source dir'),
        new nbArgument('target-dir', nbArgument::REQUIRED, 'Target dir')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit',         'x', nbOption::PARAMETER_NONE,     'Execute synchronization'),
        new nbOption('delete',       'd', nbOption::PARAMETER_NONE,     'Deletes from remote'),
        new nbOption('exclude-from', 'e', nbOption::PARAMETER_REQUIRED, 'Exclude file'),
        new nbOption('include-from', 'i', nbOption::PARAMETER_REQUIRED, 'Include file')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Starting folder synchronization');
    $exclude = '';
    $include = '';
    
    if(isset($options['config-file'])) {
     
      $config = $this->parser->checkDefaultConfigurationDirs($options['config-file']);
    
      $pluginConfigDir = nbConfig::get('nb_plugins_dir') . '/nbFileSystemPlugin/config/';

      // Check configuration
      $checker = new nbConfigurationChecker();

      try {
        $checker->checkConfigFile($pluginConfigDir . $this->getTemplateConfigFilename(), $config, array(
          'logger' => $this->getLogger(), 
          'verbose' => $this->isVerbose()
        ));
      }
      catch(Exception $e) {
        $this->logLine('<error>Configuration file doesn\'t match the template</error>');

        $printer = new nbConfigurationPrinter();
        $printer->addConfiguration(nbConfig::getAll());
        $printer->addConfigurationFile($config);      
        $printer->addConfigurationErrors($checker->getErrors());

        $this->logLine($printer->printAll());

        return false;
        //throw $e;
      }
    }
    
    if(isset($options['exclude-from']) && file_exists($options['exclude-from']))
      $exclude = ' --exclude-from \'' . $options['exclude-from'] . '\' ';
    
    if(isset($options['include-from']) && file_exists($options['include-from']))
      $include = ' --include-from \'' . $options['include-from'] . '\' ';
    
    $doit   = isset($options['doit']) ? '' : '--dry-run';
    $delete = isset($options['delete']) ? '--delete' : '';
    
    // Trailing slash must be added after sanitize dir
    $sourceDir = nbFileSystem::sanitizeDir($arguments['source-dir']) . '/';
    $targetDir = nbFileSystem::sanitizeDir($arguments['target-dir']);
    
    $cmd = sprintf('rsync -azoChpA %s %s %s %s %s %s %s', ($this->isVerbose() ? '-v' : ''), $doit, $include, $exclude, $delete, $sourceDir, $targetDir);
    
    $this->executeShellCommand($cmd);
    $this->logLine('Folders synchronization completed');
    
    return true;
  }

}
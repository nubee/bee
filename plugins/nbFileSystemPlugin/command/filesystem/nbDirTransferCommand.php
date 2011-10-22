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
    $shell = new nbShell();
    $exclude = '';
    $include = '';
    $doit = '--dry-run';
    $delete = '';
    
    // Trailing slash must be added after sanitize dir
    $sourceDir = nbFileSystem::sanitizeDir($arguments['source-dir']) . '/';
    $targetDir = nbFileSystem::sanitizeDir($arguments['target-dir']);
    
    if(isset($options['exclude-from']) && file_exists($options['exclude-from']))
      $exclude = ' --exclude-from \'' . $options['exclude-from'] . '\' ';
    
    if(isset($options['include-from']) && file_exists($options['include-from']))
      $include = ' --include-from \'' . $options['include-from'] . '\' ';
    
    if(isset($options['doit']))
      $doit = '';
    
    if(isset($options['delete']))
      $delete = '--delete';
    
    $cmd = sprintf('rsync -azvoChpA %s %s %s %s %s %s', $doit, $include, $exclude, $delete, $sourceDir, $targetDir);
    
    $shell->execute($cmd);
    $this->logLine('Folders synchronization completed');
    
    return true;
  }

}
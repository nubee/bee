<?php

class nbInflateDirCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('archive:inflate-dir')
      ->setBriefDescription('Inflates a directory in gzip format file')
      ->setDescription(<<<TXT
 The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('source-dir', nbArgument::REQUIRED, 'Source directory'),
        new nbArgument('archive-dir', nbArgument::REQUIRED, 'Archive directory')
      )));
    
    $this->setOptions(new nbOptionSet(array(
        new nbOption('create-archive-dir', '', nbOption::PARAMETER_NONE, 'Create archive dir if not exists'),
      )));    
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $timestamp = date('YmdHi', time());
    $sourceDir = nbFileSystem::sanitizeDir($arguments['source-dir']);
    $archiveDir = nbFileSystem::sanitizeDir($arguments['archive-dir']);
    
    $createArchiveDir = isset($options['create-archive-dir']);
    
    if(!is_dir($sourceDir)) 
      throw new Exception("Source directory not found: " . $sourceDir);

    if(!is_dir($archiveDir)) {
      if(!$createArchiveDir) 
        throw new Exception("Archive directory not found. " . $archiveDir);
      
      $this->getFileSystem()->mkdir($archiveDir, true);
    }
    
    $targetDir = basename($sourceDir);
    $targetFile = $targetDir . '-' . $timestamp . '.tgz';
    $this->logLine(sprintf('Archiving %s in %s/%s', $sourceDir, $archiveDir, $targetFile));
    
    $cmd = sprintf('tar -czvf "%s/%s" %s', $archiveDir, $targetFile, $sourceDir);
    
    $this->executeShellCommand($cmd);

    $this->logLine(sprintf('Directory inflated: %s in %s ', $sourceDir, $targetFile));
    
    return true;
  }

}
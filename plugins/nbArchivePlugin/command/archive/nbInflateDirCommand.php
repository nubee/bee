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
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $timestamp = date('YmdHi', time());
    $sourceDir = nbFileSystem::sanitizeDir($arguments['source-dir']);
    $archiveDir = nbFileSystem::sanitizeDir($arguments['archive-dir']);
    
    if(!is_dir($sourceDir)) 
      throw new Exception("Source directory not found: " . $sourceDir);

    if(!is_dir($archiveDir)) 
      throw new Exception("Archive directory not found. " . $archiveDir);
    
    $targetDir = basename($sourceDir);
    $targetFile = $targetDir . '-' . $timestamp . '.tgz';
    $this->logLine(sprintf('Archiving %s in %s/%s', $sourceDir, $archiveDir, $targetFile));
    
    $cmd = sprintf('tar -czvf %s/%s %s', $archiveDir, $targetFile, $sourceDir);
    
    $shell = new nbShell();
    $shell->execute($cmd);

    $this->logLine(sprintf('Directory inflated: %s in %s ', $sourceDir, $targetFile));
    
    return true;
  }

}
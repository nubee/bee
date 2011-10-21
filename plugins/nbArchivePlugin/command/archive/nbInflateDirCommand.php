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
        new nbArgument('target-path', nbArgument::REQUIRED, 'Target path'),
        new nbArgument('target-dir', nbArgument::REQUIRED, 'Target directory'),
        new nbArgument('archive-path', nbArgument::REQUIRED, 'Archive path')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $timestamp = date('YmdHi', time());
    $targetPath = nbFileSystem::sanitizeDir($arguments['target-path']);
    $targetDir = nbFileSystem::sanitizeDir($arguments['target-dir']);
    $archivePath = nbFileSystem::sanitizeDir($arguments['archive-path']);
    
    if(!file_exists($targetPath . '/' . $targetDir)) 
      throw new Exception("dir to inflate not found: " . $targetPath . '/' . $targetDir);

    if(!file_exists($archivePath)) 
      throw new Exception("Archive path not found. " . $archivePath);
    
    $targetFile = $targetDir . '-' . $timestamp . '.tgz';
    $this->logLine(sprintf('Archiving %s/%s in %s/%s', $targetPath, $targetDir, $archivePath, $targetFile));
    
    $shell = new nbShell();
    $cmd = sprintf('tar -czvf %s/%s -C %s %s', $archivePath, $targetFile, $targetPath, $targetDir);
    
    $this->logLine('Tar command: ' . $cmd);
    $shell->execute($cmd);
    $this->logLine('Dir inflated: ' . $targetDir);
    
    return true;
  }

}
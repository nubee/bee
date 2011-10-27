<?php

class nbArchiveDirCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('archive:archive-dir')
      ->setBriefDescription('Inflates a directory in gzip format file')
      ->setDescription(<<<TXT
 The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('source-dir', nbArgument::REQUIRED, 'Source directory'),
        new nbArgument('destination-dir', nbArgument::REQUIRED, 'Destination directory')
      )));
    
    $this->setOptions(new nbOptionSet(array(
        new nbOption('create-destination-dir', '', nbOption::PARAMETER_NONE, 'Create destination dir if not exists'),
        new nbOption('filename', '', nbOption::PARAMETER_REQUIRED, 'Output filename'),
      )));    
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $timestamp = date('YmdHi', time());
    $sourceDir = nbFileSystem::sanitizeDir($arguments['source-dir']);
    $destinationDir = nbFileSystem::sanitizeDir($arguments['destination-dir']);
    $createDestinationDir = isset($options['create-destination-dir']);
    $archiveDir = basename($sourceDir);
    
    $filename = isset($options['filename']) ? $options['filename'] : sprintf('%s-%s.tar.gz', $archiveDir, $timestamp);
    $this->logLine(sprintf('Archiving %s in %s/%s', $sourceDir, $destinationDir, $filename));
    
    if(!is_dir($sourceDir)) 
      throw new InvalidArgumentException("Source directory not found: " . $sourceDir);

    if(!is_dir($destinationDir)) {
      if(!$createDestinationDir) 
        throw new InvalidArgumentException("Archive directory not found: " . $destinationDir);
      
      $this->getFileSystem()->mkdir($destinationDir, true);
    }
    
    // Options:
    // c: compress
    // v: verbose
    // z: gzip archive
    // f: archive to file
    // C: root dir in the archived file
    $cmd = sprintf('tar -c%szf "%s/%s" %s -C"%s"', $this->isVerbose() ? 'v' : '', $destinationDir, $filename, $sourceDir, $archiveDir);
    
    $this->executeShellCommand($cmd);

    $this->logLine(sprintf('Directory archived: %s in %s ', $sourceDir, $filename));
    
    return true;
  }


}
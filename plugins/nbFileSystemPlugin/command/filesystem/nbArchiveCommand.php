<?php

class nbArchiveCommand extends nbCommand
{

  protected function configure()
  {
    $timestamp = date('YmdHi', time());
    $this->setName('filesystem:archive')
      ->setBriefDescription('Archives and compress one or more directories and/or files in gzip format file')
      ->setDescription(<<<TXT
 Examples:

  Archives <comment>./source</comment> and <comment>./data/config.yml</comment> in <comment>/backup/{$timestamp}-archive.tgz</comment>
  <info>./bee filesystem:archive /backup/archive.tar.gz source/ data/config.yml --add-timestamp</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('destination', nbArgument::REQUIRED, 'Archive file destination (you have to add this file extension: tgz)'),
        new nbArgument('sources', nbArgument::IS_ARRAY | nbArgument::REQUIRED, 'Directories and/or files to archive'),
      )));
    
    $this->setOptions(new nbOptionSet(array(
        new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'Force creation of destination directory'),
        new nbOption('add-timestamp', 't', nbOption::PARAMETER_NONE, 'Prefix the archive name with a timestamp'),
      )));    
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $timestamp = isset($options['add-timestamp']) ? date('YmdHi', time()) . '-' : null;
    $sources = $arguments['sources'];
    $destination = nbFileSystem::sanitizeDir($arguments['destination']);
    $force = isset($options['force']);
    
    $archiveName = sprintf('%s%s', $timestamp, basename($destination));
    $archiveDir = dirname($destination);
    
    $this->logLine(sprintf('Creating archive <comment>%s/%s</comment>...', $archiveDir, $archiveName), nbLogger::INFO);
    
    foreach ($sources as $key => $source) {
      $sources[$key] = nbFileSystem::sanitizeDir($source);
      if(!is_dir($sources[$key]))
        if(!is_file($sources[$key]))
          throw new InvalidArgumentException("Source not found: " . $sources[$key]);
    }

    if(!is_dir($archiveDir)) {
      if(!$force)
        throw new InvalidArgumentException("Destination directory not found: " . $archiveDir);
      
      $this->getFileSystem()->mkdir($archiveDir, true);
    }

    // Options:
    // c: compress
    // v: verbose
    // z: gzip archive
    // f: archive to file
    // C: root dir in the archived file
    $cmd = sprintf('tar -czf %s/%s %s', $archiveDir, $archiveName, implode(' ', $sources));
    
    $this->executeShellCommand($cmd, true);

    if ($this->isVerbose()) {
      $logLine = sprintf("Directories/Files <comment>\n - %s\n</comment>archived in \n <comment>%s/%s</comment>",
        implode("\n - ", $sources), $archiveDir, $archiveName
      );
    }
    else {
      $logLine = sprintf("Directories/Files archived in <comment>%s/%s</comment>", $archiveDir, $archiveName);
    }
    
    $this->logLine($logLine, nbLogger::INFO);
    
    return true;
  }

}
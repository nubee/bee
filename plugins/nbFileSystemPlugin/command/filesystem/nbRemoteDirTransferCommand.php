<?php

class nbRemoteDirTransferCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('filesystem:remote-dir-transfer')
      ->setBriefDescription('Rsyncs a directory with a remote server')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('source-dir',    nbArgument::REQUIRED, 'Local source dir'),
        new nbArgument('remote-server', nbArgument::REQUIRED, 'Remote server'),
        new nbArgument('remote-user',   nbArgument::REQUIRED, 'Remote user'),
        new nbArgument('remote-dir',    nbArgument::REQUIRED, 'Remote destination dir')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit',         'x', nbOption::PARAMETER_NONE,     'Execute synchronization'),
        new nbOption('delete',       'd', nbOption::PARAMETER_NONE,     'This option set delete option'),
        new nbOption('exclude-from', 'e', nbOption::PARAMETER_REQUIRED, 'This option set the exclude file'),
        new nbOption('include-from', 'i', nbOption::PARAMETER_REQUIRED, 'This option set the include file')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Starting remote folder synchronization');
    $exclude = '';
    $include = '';
    
    // Trailing slash must be added after sanitize dir
    $sourceDir = nbFileSystem::sanitizeDir($arguments['source-dir']) . '/*';
    $remoteUser = $arguments['remote-user'];
    $remoteServer = $arguments['remote-server'];
    $remotePath = nbFileSystem::sanitizeDir($arguments['remote-dir']);
    
    if(isset($s['exclude-from']) && file_exists($s['exclude-from']))
      $exclude = ' --exclude-from \'' . $s['exclude-from'] . '\' ';
    
    if(isset($s['include-from']) && file_exists($s['include-from']))
      $include = ' --include-from \'' . $s['include-from'] . '\' ';
    
    $doit = isset($s['doit']) ? '' : '--dry-run';
    $delete = isset($s['delete']) ? '--delete' : '';
    
    $cmd = sprintf('rsync -azvoChpA %s %s %s %s %s -e ssh %s@%s:%s', 
      $doit, $include, $exclude, $delete, $sourceDir, 
      $remoteUser, $remoteServer, $remotePath);
    
    $this->executeShellCommand($cmd);
    $this->logLine('Remote folder synchronization completed');
  }

}
<?php

class nbRemoteDirTransferCommand extends nbCommand {

  protected function configure() {
    $this->setName('filesystem:remote-dir-transfer')
            ->setBriefDescription('Rsyncs a directory with a remote server')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('source-path', nbArgument::REQUIRED, 'Local source path'),
                new nbArgument('remote-server', nbArgument::REQUIRED, 'Remote server'),
                new nbArgument('remote-user', nbArgument::REQUIRED, 'Remote user'),
                new nbArgument('remote-path', nbArgument::REQUIRED, 'Remote destination path')
            )));

    $this->setOptions(new nbOptionSet(array(
                new nbOption('config-file', 'f', nbOption::PARAMETER_OPTIONAL, 'FileSystem plugin configuration file', './.bee/nbFileSystemPlugin.yml'),
                new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'This option execute syncronization'),
                new nbOption('delete', 'd', nbOption::PARAMETER_NONE, 'This option set delete option'),
                new nbOption('exclude-from', 'e', nbOption::PARAMETER_REQUIRED, 'This option set the exclude file'),
                new nbOption('include-from', 'i', nbOption::PARAMETER_REQUIRED, 'This option set the include file')
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Start remote folder syncronization');
    $shell = new nbShell();
    $excludeOption = '';
    $includeOption = '';
    $doitOption = 'n';
    $deleteOption = '';
    if (isset($options['exclude-from']) && file_exists($options['exclude-from']))
      $excludeOption = ' --exclude-from \'' . $options['exclude-from'] . '\' ';
    if (isset($options['include-from']) && file_exists($options['include-from']))
      $includeOption = ' --include-from \'' . $options['include-from'] . '\' ';
    if (isset($options['doit']))
      $doitOption = '';
    if (isset($options['delete']))
      $deleteOption = '--delete';
    $cmd = 'rsync -azvCh'.$doitOption.' '.
      $includeOption.' '.
      $excludeOption.' '.
      $deleteOption. ' '.
      nbFileSystemUtils::sanitize_dir($arguments['source-path']).'/* '.
      '-e ssh '.$arguments['remote-user'].'@'.$arguments['remote-server'] .':'.nbFileSystemUtils::sanitize_dir($arguments['remote-path']);
    $this->logLine($cmd);
    $shell->execute($cmd);
    $this->logLine('Done remote folder syncronization');
    return true;
  }

}
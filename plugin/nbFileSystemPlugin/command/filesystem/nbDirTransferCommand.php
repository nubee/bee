<?php

class nbDirTransferCommand extends nbCommand {

  protected function configure() {
    $this->setName('filesystem:remote-dir-transfer')
            ->setBriefDescription('rsync a directory with a remote server')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('source-folder', nbArgument::REQUIRED, 'Local source path'),
                new nbArgument('remote-server', nbArgument::REQUIRED, 'Remote server'),
                new nbArgument('remote-user', nbArgument::REQUIRED, 'Remote user'),
                new nbArgument('remote-folder', nbArgument::REQUIRED, 'Remote destination path')
            )));

    $this->setOptions(new nbOptionSet(array(
                new nbOption('config-file', 'f', nbOption::PARAMETER_OPTIONAL, 'Archive configuration file', './.bee/nbFileSystemPlugin.yml'),
                new nbOption('doit', 'd', nbOption::PARAMETER_NONE, 'This option execute syncronization'),
                new nbOption('exclude-from', 'e', nbOption::PARAMETER_REQUIRED, 'This option set the exclude file')
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Start folder syncronization');
    $shell = new nbShell();
    $exclude_option = '';
    $doit_option = 'n';
    //'rsync -azvCh  --exclude-from './exclude.txt' --delete '.$arguments['source_project_path'].' '.$arguments['target_project_path']
    if (isset($options['exclude-from']) && file_exists($options['exclude-from']))
      $exclude_option = ' --exclude-from \'' . $options['exclude-from'] . '\' ';
    if (isset($options['doit']))
      $doit_option = '';
    $shell->execute('rsync -azvCh' . $doit_option . ' ' . $exclude_option . ' --delete --include=core ' . $arguments['source-folder'] . '/* -e ssh ' . $arguments['remote-user'] . '@' . $arguments['remote-server'] . ':' . $arguments['remote-folder']);
    $this->logLine('Done folder syncronization');
    return true;
  }

}
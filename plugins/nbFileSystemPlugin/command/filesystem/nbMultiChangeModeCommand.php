<?php

class nbMultiChangeModeCommand extends nbCommand {

  protected function configure() {
    $this->setName('filesystem:multi-change-mode')
      ->setBriefDescription('Changes directory and file modes for a list of directories')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('list-file', nbArgument::REQUIRED, 'Yml file containing a list of directories with dir/file modes'),
      )));
    
    $this->setOptions(new nbOptionSet(array(
        new nbOption('doit', 'x', nbOption::PARAMETER_NONE, 'Make the changes!'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $listFile = $arguments['list-file'];
    $doit     = isset($options['doit']);
    
    $configuration = new nbConfiguration();
    $configuration->add(nbConfig::getAll());
    $configuration->add(sfYaml::load($listFile), '', true);
    
    foreach ($configuration->get('filesystem_multi-change-mode') as $item) {
      $directory = $item['directory'];
      $dirMode = $item['dir-mode'];
      $fileMode = $item['file-mode'];
      
      $this->logLine(sprintf('Changing directory mode in %s for %s', $dirMode, $directory));
      $command = sprintf('find %s -type d -exec chmod %s {} \\;', $directory, $dirMode);
      $this->executeShellCommand($command, $doit);

      $this->logLine(sprintf('Changing file mode in %s for files in %s', $fileMode, $directory));
      $command = sprintf('find %s -type f -exec chmod %s {} \\;', $directory, $fileMode);
      $this->executeShellCommand($command, $doit);
    }

    $this->logLine('Mode changed successfully!');

    return true;
  }

}
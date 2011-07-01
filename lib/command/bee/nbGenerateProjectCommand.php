<?php

class nbGenerateProjectCommand extends nbCommand {

  protected function configure() {
    $this->setName('bee:generate-project')
            ->setBriefDescription('Generate a bee project into a folder')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('project-dir', nbArgument::OPTIONAL, 'Project directory', '.'),
            )));

    $this->setOptions(new nbOptionSet(array(
                new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'Overwrites the existing configuration'),
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $configDir = nbFileSystemUtils::sanitize_dir($arguments['project-dir']) . '/.bee';
    if (isset($options['force']))
      $force = true;
    else
      $force = false;
    nbFileSystemUtils::mkdir($configDir, $force);
    nbFileSystem::copy(nbConfig::get('nb_bee_dir','.').'/data/config/bee.yml', $configDir.'/bee.yml', $force);
    nbFileSystem::copy(nbConfig::get('nb_bee_dir','.').'/data/config/config.yml', $configDir.'/config.yml', $force);
    return true;
  }

}
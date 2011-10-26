<?php

class nbGenerateProjectCommand extends nbCommand
{

  protected function configure()
  {
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

  protected function execute(array $arguments = array(), array $options = array())
  {
    $projectDir = $arguments['project-dir'];
    $this->logLine(sprintf('Generating bee project in %s', $projectDir));
    $fs        = $this->getFileSystem();
    $configDir = $fs->sanitizeDir($projectDir) . '/.bee';
    $force     = isset($options['force']) ? true : false;

    $fs->mkdir($configDir, true);
    $fs->copy(nbConfig::get('nb_bee_dir') . '/data/config/bee.yml', $configDir . '/bee.yml', $force);
    $fs->copy(nbConfig::get('nb_bee_dir') . '/data/config/config.yml', $configDir . '/config.yml', $force);

    $this->logLine('bee project generated!');
    
    return true;
  }

}
<?php

class nbTestAllCommand extends nbApplicationCommand {

  protected function configure() {
    $this->setName('test:all')
            ->setBriefDescription('Launch all tests for a project')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array()));

    $this->setOptions(new nbOptionSet(array()));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    if (nbConfig::has('project_type')) {
      $projectType = nbConfig::get('project_type');

      if ($projectType == "bee") {
        $unitTest = new nbBeeTestUnitCommand();
        $unitTest->run(new nbCommandLineParser(), '', true);

        $pluginsTest = new nbBeeTestPluginsCommand();
        $pluginsTest->run(new nbCommandLineParser(), '', true);
      } else {
        try {
          $commandSet = $this->getApplication()->getCommands();
          $testAllCmd = $commandSet->getCommand($projectType . ':test-all');
          $testAllCmd->run(new nbCommandLineParser(), '', true);
        } catch (Exception $e) {
          $this->logLine($e->getMessage(), nbLogger::ERROR);
        }
      }
    }
    else throw new Exception('Project type not defined');
  }
  
    

}
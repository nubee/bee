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
        $retUnit = $unitTest->run(new nbCommandLineParser(), '', true);

        $pluginsTest = new nbBeeTestPluginsCommand();
        $retPlugin = $pluginsTest->run(new nbCommandLineParser(), '', true);
        if($retUnit == 0 or $retPlugin ==0)
          return 0;
        else
          return 1;
      } else {
        try {
          $commandSet = $this->getApplication()->getCommands();
          $testAllCmd = $commandSet->getCommand($projectType . ':test-all');
          $testAllCmd->run(new nbCommandLineParser(), '', true);
          return true;
        } catch (Exception $e) {
          $this->logLine($e->getMessage(), nbLogger::ERROR);
          return false;
        }
      }
    }
    else throw new Exception('Project type not defined');
  }
  
    

}
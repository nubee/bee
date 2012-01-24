<?php

class nbAndroidTestAllCommand extends nbCommand {

  protected function configure() {
    $this->setName('android:test-all')
            ->setBriefDescription('launch android unit tests')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
            )));

    $this->setOptions(new nbOptionSet(array(
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {

    if (nbConfig::has('project_android_test_build-file')) {
      $buildFile = nbConfig::get('project_android_test_build-file');
    }
    else
      throw new Exception('"project_android_test_build-file" not set');
    if (nbConfig::has('project_type') && nbConfig::get('project_type') == 'android') {
      $cmd = sprintf('ant -f %s test', $buildFile);
      $this->executeShellCommand($cmd);
      return true;
    }
    else
      throw new Exception('This isn\'t a android project');
  }

}
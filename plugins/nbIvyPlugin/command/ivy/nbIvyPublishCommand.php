<?php

class nbIvyPublishCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('ivy:publish')
      ->setBriefDescription('Publishes dependencies into local repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info>:

    <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->addOption(
      new nbOption('local', 'l', nbOption::PARAMETER_NONE, 'Publish into local repository')
    );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $client = new nbIvyClient();

    $this->logLine('Publishing... ', nbLogger::COMMENT);
    $command = $client->getPublishCmdLine(isset($options['local']));

    $this->executeShellCommand($command);
  }

}

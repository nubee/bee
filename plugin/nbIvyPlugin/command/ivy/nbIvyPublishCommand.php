<?php

class nbIvyPublishCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('ivy:publish')
      ->setOptions(new nbOptionSet(array(
        new nbOption('local', 'l', nbOption::PARAMETER_NONE, 'Publish into local repository')
      )))
      ->setBriefDescription('Publish into local repository')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info>:

    <info>./bee {$this->getFullName()}</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $shell = new nbShell();
    $client = new nbIvyClient();

    $this->log('Publishing...', nbLogger::COMMENT);
    $this->log("\n");
    $command = $client->getPublishCmdLine(isset($options['local']));
    echo $command . "\n"; die;

    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbIvyPublishCommand::execute] Error executing command:
  %s
",
        $command
      ));
    }
  }
}

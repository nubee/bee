<?php

class nbGtestTestCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('gtest:test')
      ->setBriefDescription('Run gtest tests')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> run gtest tests:

    <info>./bee {$this->getFullName()}</info>
TXT
      );
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $shell = new nbShell();
    $client = new nbAntClient();

    $this->log('Running test', nbLogger::COMMENT);
    $this->log("\n");
    $command = $client->getCommandLine('run-tests');

    if(!$shell->execute($command)) {
      throw new LogicException(sprintf("
[nbGtestTestCommand::execute] Error executing command:
  %s
",
        $command
      ));
    }
  }
}

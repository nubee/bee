<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test();

$fooArgument = new nbArgument('foo');
$barOption = new nbOption('bar');
$command1 = new DummyCommand("foo");
$command2 = new DummyCommand("ns:bar", new nbArgumentSet(array($fooArgument)));
$command3 = new DummyCommand("ns2:bas", null, new nbOptionSet(array($barOption)));

class TestChainCommand extends nbChainCommand
{
  protected function configure()
  {
    $this->setName('dummy:test')
      ->setBriefDescription('TestChainCommand')
      ->setDescription('');
  }
  public function setCommandChain(array $commands) {
    $this->commands = $commands;
  }
}

$chainCommand = new TestChainCommand();

$t->comment('nbChainCommandTest - Test empty chain');
$t->ok($chainCommand->run(new nbCommandLineParser(array(), array()), ''));

$t->comment('nbChainCommandTest - Test one command in chain');
$chainCommand->setCommandChain(array($command1));
$t->ok($chainCommand->run(new nbCommandLineParser(array(), array()), ''));
$t->ok($command1->hasExecuted());

$t->comment('nbChainCommandTest - Test two commands in chain');
$chainCommand->setCommandChain(array($command1, $command2));
$t->ok($chainCommand->run(new nbCommandLineParser(array(), array()), ''));
$t->ok($command1->hasExecuted());
$t->ok($command2->hasExecuted());

$t->comment('nbChainCommandTest - Test two commands in chain, first failing');
$chainCommand->setCommandChain(array($command1, $command2));
$command1->failOnExecute();
$t->ok(!$chainCommand->run(new nbCommandLineParser(array(), array()), ''));
$t->ok($command1->hasExecuted());
$t->ok($command2->hasExecuted());

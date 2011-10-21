<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

class testShellCommand extends nbShellExecuteCommand
{
  public function getAlias($command)
  {
    return parent::getAlias($command);
  }
}

$shellCommand = new testShellCommand();
$t = new lime_test();

$t->is($shellCommand->getAlias('command'),null,'->getAlias() returns null if param isn\'t an alias');
nbConfig::set('project_shell_aliases_command','realCommand');
nbConfig::set('project_shell_aliases_anotheralias','another real command');
$t->is($shellCommand->getAlias('command'),'realCommand','->getAlias() returns real command if param is an alias');
$t->is($shellCommand->getAlias('anotheralias'),'another real command','->getAlias() returns real command if param is an alias');

$t->comment('nbShellExecuteCommandTest - pass args value to command line');
ob_start();
$t->ok(!$shellCommand->run(new nbCommandLineParser(array(), array()), 'dir --args="-Doption=true"'));
$contents = ob_get_contents();
ob_end_clean();

ob_start();
$t->ok($shellCommand->run(new nbCommandLineParser(array(), array()), 'dir'));
$contents = ob_get_contents();
ob_end_clean();

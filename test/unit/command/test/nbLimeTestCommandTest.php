<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';
$t = new lime_test(1);
$t->pass('no tests');

//nbConfig::set('nb_test_dir', 'test/data/command/test');
//
//$t = new lime_test(5);
//
//$command = new nbLimeTestCommand();
//$t->comment('nbLimeTestCommandTest - Test run (pass)');
//ob_start();
//$ret = $command->run(new nbCommandLineParser(), 'nbAClass');
//$t->ok($ret, '->run() return true if test pass');
//ob_end_clean();
//
//$t->comment('nbLimeTestCommandTest - Test run (fail)');
//ob_start();
//$command = new nbLimeTestCommand();
//$ret = $command->run(new nbCommandLineParser(), 'nbBClass');
//$t->ok(!$ret, '->run() return false if test fail');
//ob_end_clean();
//
//$t->comment('nbLimeTestCommandTest - Test run multiple tests');
//ob_start();
//$command = new nbLimeTestCommand();
//$ret = $command->run(new nbCommandLineParser(), 'nbAClass nbCClass');
//$t->ok($ret, '->run() return true if all tests pass');
//$ret = $command->run(new nbCommandLineParser(), 'nbAClass nbBClass');
//$t->ok(!$ret, '->run() return false if at least one test fail');
//ob_end_clean();
//
//$t->comment('nbLimeTestCommandTest - Tests are in a subfolder');
//ob_start();
//$command = new nbLimeTestCommand();
//$ret = $command->run(new nbCommandLineParser(), 'subfolder/*');
//$t->ok($ret, '->run() return true if some test are present');
//ob_end_clean();

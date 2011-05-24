<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');
$t = new lime_test(0);

$browser = new sfTestFunctional(new sfBrowser());
$browser->info('1 - Homepage')->
  get('/')->
  with('request')->begin()->
    isParameter('module', 'main')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end()
;

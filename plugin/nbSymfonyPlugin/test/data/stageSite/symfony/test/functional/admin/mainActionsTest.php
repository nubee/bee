<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->info('1 - Login')->
  get('/')->
  click('Signin', array('signin' => array(
      'username'   => 'dealer1@example.com',
      'password' => 'dealer1pwd'
    )))->
  with('response')->
    isRedirected()->
    followRedirect()->
  with('request')->begin()->
    isParameter('module', 'main')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '!/This is a temporary page/')->
  end()
;

$browser->info('2 - Deal links')->
  info('  2.1 - Click Current deals')->
  get('/')->
  click('Current deals')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'current')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end()
;

$browser->info('  2.2 - Click Expiring deals')->
  get('/')->
  click('Expiring deals')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'expiring')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end()
;

$browser->info('  2.3 - Click Expired deals')->
  get('/')->
  click('Expired deals')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'expired')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end()
;
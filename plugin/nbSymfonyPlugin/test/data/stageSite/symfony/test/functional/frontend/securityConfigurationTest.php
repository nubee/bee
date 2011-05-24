<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');
$t = new lime_test(0);

$browser = new FindealTestFunctional(new sfBrowser());
$browser->loadData();

$browser->info('1 - Anonymous user permissions')->
  info('  1.1 - homepage')->

  get('/')->
  with('request')->begin()->
    isParameter('module', 'main')->
    isParameter('action', 'index')->

  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end()
;
$browser->
  info('  1.2 - forbidden page')->
  get('/private')->
  with('request')->begin()->
    isParameter('module', 'main')->
    isParameter('action', 'private')->

  end()->
  info('  1.3 - forward to login page')->
  with('response')->begin()->
    isStatusCode(401)->
  end()
;

$browser->info('2 - Customer user permissions')->
  info('  2.1 - forbidden page')->
  get('/private')->
      click('Signin', array('signin' => array(
          'username'   => 'customer1@example.com',
          'password' => 'customer1pwd'
        )))->
        with('response')->
        isRedirected()->
        followRedirect()->
      with('request')->begin()->
        isParameter('module', 'main')->
        isParameter('action', 'private')->
  end()->
  with('response')->begin()->
        isStatusCode(200)->
        checkElement('body', '/Welcome\suser\scustomer1\@example\.com/')->
  end()->
  get('/logout')->
  with('response')->
        isRedirected()->
        followRedirect()->
      with('request')->begin()->
        isParameter('module', 'main')->
        isParameter('action', 'private')->
       end()->
      with('response')->begin()->
        isStatusCode(401)->

end()
;

$browser->info('3 - Dealer user permissions')->
  info('  3.1 - forbidden page')->
  get('/private')->
      click('Signin', array('signin' => array(
          'username'   => 'dealer1@example.com',
          'password' => 'dealer1pwd'
        )))->
        with('response')->
        isRedirected()->
        followRedirect()->
      with('request')->begin()->
        isParameter('module', 'main')->
        isParameter('action', 'private')->
  end()->
  with('response')->begin()->
        isStatusCode(200)->
        checkElement('body', '/Welcome\suser\sdealer1@example.com/')->
  end()->
    get('/logout')->
  with('response')->
        isRedirected()->
        followRedirect()->
      with('request')->begin()->
        isParameter('module', 'main')->
        isParameter('action', 'private')->
       end()->
      with('response')->begin()->
        isStatusCode(401)->

end()
;


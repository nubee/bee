<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');
$t = new lime_test(0);

$browser = new FindealTestFunctional(new sfBrowser());
$browser->loadData();

$browser->info('1 - User Registration')->
  info('  1.1 - Registration form')->

  get('/register')->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'new')->

  end()->
  click('Submit', array('sf_guard_user' => array(
    'email_address'      => 'userNotVerified@example.com',
    'password'           => 'userNotVerifiedpwd',
  )))->
  info('  1.2 - User registration')->
  with('form')->begin()->
    hasErrors(false)->
  end()->
  info('  1.3 - check activation mail')->
  with('mailer')->begin()->
    checkHeader('Subject', 'Findeal account activation')->
    checkBody('/To fully activate your Findeal account/')->
    checkBody('#'.$activationLink.'n\w+#')->
  end()->
  followRedirect()->
  info('  1.4 - User redirect to homepage')->
  with('request')->begin()->
    isParameter('module', 'main')->
    isParameter('action', 'index')->
  end()->
  get('/logout')->
  with('response')->
        isRedirected()->
        followRedirect()->
      with('request')->begin()->
        isParameter('module', 'main')->
        isParameter('action', 'index')->
       end()
;


$browser->info('2 - User with email not verified Login')->
  info('  2.1 - forbidden page for anonymous')->
  get('/private')->
      click('Signin', array('signin' => array(
          'username'   => 'userNotVerified@example.com',
          'password' => 'userNotVerifiedpwd'
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
        checkElement('body', '/Welcome\suser\suserNotVerified\@example\.com/')->
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


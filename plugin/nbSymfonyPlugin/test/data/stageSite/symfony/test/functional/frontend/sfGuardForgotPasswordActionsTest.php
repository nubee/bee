<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');
$t = new lime_test(1);

$browser = new FindealTestFunctional(new sfBrowser());
$browser->loadData();
$forgotPasswordLink = sfConfig::get('app_forgot_password_url');

$browser->info('1 - User Forgot password')->
  info('  1.1 - Forgot password form')->

  get('/guard/forgot_password')->
  with('request')->begin()->
    isParameter('module', 'sfGuardForgotPassword')->
    isParameter('action', 'index')->

  end()->
  click('Request', array('forgot_password' => array(
    'email_address'      => 'forgot@findeal.com'
  )))->
  info('  1.3 - Request new password')->
  with('form')->begin()->
    hasErrors(false)->
  end()->
  info('  1.3 - check forgot password mail')->
  with('mailer')->
  //debug()->
  begin()->
    checkHeader('Subject', 'Forgot Password Request for forgot@findeal.com')->
    checkBody('#'.$forgotPasswordLink.'\w+#')->
  end()->
  followRedirect()->

  info('  1.4 - Redirect to login page')->
  with('request')->begin()->
    isParameter('module', 'sfGuardAuth')->
    isParameter('action', 'signin')->
  end()
;
$user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress('forgot@findeal.com');
$t->comment('forgot password: verify that the request for changing password was accepted');
$t->ok($uniqueKey = $user->getForgotPassword()->getUniqueKey(),$uniqueKey);

$browser->info('2 - Change user password')->
  info('  2.1 - Change password form')->

  get('/guard/forgot_password/'.$uniqueKey)->
  with('request')->begin()->
    isParameter('module', 'sfGuardForgotPassword')->
    isParameter('action', 'change')->

  end()->
  click('Change', array('sf_guard_user' => array(
    'password'      => 'newpwd',
    'password_again'      => 'newpwd'
  )))->
  info('  1.3 - Request new password')->
  with('form')->begin()->
    hasErrors(false)->
  end()->
  info('  1.3 - check change password mail')->
  with('mailer')->
  //debug()->
  begin()->
    checkHeader('Subject', 'New Password for forgot@findeal.com')->
    checkBody('/Password: newpwd/')->
  end()->
  followRedirect()->

  info('  1.4 - Redirect to login page')->
  with('request')->begin()->
    isParameter('module', 'sfGuardAuth')->
    isParameter('action', 'signin')->
  end()
;

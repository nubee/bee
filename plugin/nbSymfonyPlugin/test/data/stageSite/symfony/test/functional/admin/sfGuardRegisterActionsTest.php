<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');
$t = new lime_test(19);

$activationLink = sfConfig::get('app_activation_url');
$findealAdminLink = sfConfig::get('app_admin_url');

$browser = new FindealTestFunctional(new sfBrowser());
$browser->loadData();

$browser->info('1 - Manager Registration')->
  info('  1.1 - Registration form')->

  get('/register')->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'new')->

  end()->
  click('Submit', array('sf_guard_user' => array(
    'email_address'      => 'manager1@example.com',
    'password'           => 'manager1pwd',
  )))->
  info('  1.2 - New user registration')->
  with('form')->begin()->
    hasErrors(false)->
  end()->
  info('  1.3 - check activation mail')->
  with('mailer')->begin()->
    checkHeader('Subject', 'Findeal manager account activation')->
    checkBody('/To fully activate your Findeal account/')->
    checkBody('#'.$activationLink.'n\w+#')->
  end()->
  followRedirect()->

  info('  1.4 - Wait for email activation link')->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'thankYou')->
  end()
;

$user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress('manager1@example.com');
$t->comment('User registration: verify that the user fields were inserted in the database');
$t->is($user->getUsername(), 'manager1@example.com',"::getUsername");
$t->is($user->getIsActive(), 0,"::getIsActive");
$groups = $user->getGroups();

$t->is($groups->count(), 0,"::getGroups  count");

$browser->
  info('  1.5 - Customer user with email validated registration')->

  get('/register')->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'new')->

  end()->
  click('Submit', array('sf_guard_user' => array(
    'email_address'      => 'customertomanager@example.com',
    'password'           => 'ctmpwd',
  )))->
  
  info('  1.6 - check welcome mail')->
  with('mailer')->begin()->
    checkHeader('Subject', 'Findeal manager account activated')->
    checkBody('/Your Findeal manager account is active/')->
    checkBody('#'.$findealAdminLink.'#')->
  end()->
  followRedirect()->

  info('  1.7 - Login')->
  with('request')->begin()->
    isParameter('module', 'main')->
    isParameter('action', 'index')->
  end()->
  info('  1.8 - Logout')->
  get('/logout')->
  with('request')->begin()->
    isParameter('module', 'sfGuardAuth')->
    isParameter('action', 'signout')->
  end()

;

$user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress('customertomanager@example.com');
$t->comment('User registration: verify that the user fields were inserted in the database');
$t->is($user->getUsername(), 'customertomanager@example.com',"::getUsername");
$t->is($user->getIsActive(), 1,"::getIsActive");
$groups = $user->getGroups();

$t->is($groups->count(), 1,"::getGroups  count");
$t->is($groups[0]->getName(),'Managers',"::getName");

$browser->
  info('  1.9 - Customer user with email not validated registration')->

  get('/register')->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'new')->

  end()->
  click('Submit', array('sf_guard_user' => array(
    'email_address'      => 'customernotvalidated@example.com',
    'password'           => 'ctmnvpwd',
  )))->
  info('  1.10 - check activation mail')->
  with('mailer')->begin()->
    checkHeader('Subject', 'Findeal manager account activation')->
    checkBody('/To fully activate your Findeal account/')->
    checkBody('#'.$activationLink.'n\w+#')->
  end()->
  followRedirect()->

  info('  1.11 - Wait for email activation link')->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'thankYou')->
  end()
;


$user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress('customernotvalidated@example.com');
$t->comment('User registration: verify that the user fields were inserted in the database');
$t->is($user->getUsername(), 'customernotvalidated@example.com',"::getUsername");
$t->is($user->getIsActive(), 1,"::getIsActive");
$groups = $user->getGroups();

$t->is($groups->count(), 1,"::getGroups  count");
$t->is($groups[0]->getName(),'LimitedCustomers',"::getName");


$browser->info('2 - User Form validation')->
  info('  2.1 - Register a User with an invalid email')->

  get('/register')->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'new')->

  end()->
  click('Submit', array('sf_guard_user' => array(
    'email_address'      => 'fakemail',
    'password'           => 'pwduser2',
  )))->
  with('form')->begin()->
    hasErrors(1)->
    isError('email_address', 'invalid')->
  end()
;
$browser->
  info('  2.2 - Register a User with an invalid password')->

  get('/register')->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'new')->

  end()->
  click('Submit', array('sf_guard_user' => array(
    'email_address'      => 'user2@findeal.com',
    'password'           => 'sp', //a too short password
  )))->
  with('form')->begin()->
    hasErrors(1)->
    isError('password', 'min_length')->
  end()
;
$user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress('manager1@example.com');
$activationTokenManager1 = $user->getProfile()->getValidate();
$browser->info('3 - Manager User Activation')->
  info('  3.1 - Activation link')->

  get('/register/activation/'.$activationTokenManager1)->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'activation')->

  end()->
  followRedirect()->
  info('  3.2 - User logged in')->
  with('request')->begin()->
    isParameter('module', 'main')->
    isParameter('action', 'index')->
end()
;
$user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress('manager1@example.com');
$t->comment('User registration: verify that the user fields were inserted in the database');
$user->refresh(true);
$t->is($user->getUsername(), 'manager1@example.com',"::getUsername");
$t->is($user->getIsActive(), 1,"::getIsActive");
$groups = $user->getGroups();

$t->is($groups->count(), 1,"::getGroups  count");
$t->is($groups[0]->getName(),'Managers',"::getName");

$user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress('customernotvalidated@example.com');
$activationTokenCustomerNotValidated = $user->getProfile()->getValidate();
$browser->
  info('  3.3 - Customer to Manager Activation')->

  get('/register/activation/'.$activationTokenCustomerNotValidated)->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'activation')->

  end()->
  followRedirect()->
  info('  3.4 - User logged in')->
  with('request')->begin()->
    isParameter('module', 'main')->
    isParameter('action', 'index')->
end()
;
$user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress('customernotvalidated@example.com');
$t->comment('User registration: verify that the user fields were inserted in the database');
$user->refresh(true);
$t->is($user->getUsername(), 'customernotvalidated@example.com',"::getUsername");
$t->is($user->getIsActive(), 1,"::getIsActive");
$groups = $user->getGroups();

$t->is($groups->count(), 1,"::getGroups  count");
$t->is($groups[0]->getName(),'Managers',"::getName");



$fakeToken ='xxxxxxxxxx';
$browser->
  info('  3.3 - fake')->

  get('/register/activation/'.$fakeToken)->
  with('request')->begin()->
    isParameter('module', 'sfGuardRegister')->
    isParameter('action', 'activation')->

  end()->
  with('response')->begin()->
    isStatusCode(404)->
  end()
;

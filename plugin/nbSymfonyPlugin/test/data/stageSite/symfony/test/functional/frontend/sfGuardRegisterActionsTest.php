<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');
$t = new lime_test(8);

$activationLink = sfConfig::get('app_activation_url');
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
    'email_address'      => 'user1@findeal.com',
    'password'           => 'pwduser1',
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

  info('  1.4 - User logged as LimitedCustomer')->
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


$user = Doctrine::getTable('sfGuardUser')->findOneByEmailAddress('user1@findeal.com');
$t->comment('User registration: verify that the user fields were inserted in the database');
$t->is($user->getUsername(), 'user1@findeal.com',"::getUsername");
$t->is($user->getIsActive(), 1,"::getIsActive");
$groups = $user->getGroups();

if ($t->is($groups->count(), 1,"::getGroups count")){
  $t->is($groups[0]->getName(),'LimitedCustomers',"::getName LimitedCustomers");
}
else
{
  $t->fail('User without a group');
}

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

$activationTokenUser1 = $user->getProfile()->getValidate();
$browser->info('3 - User Activation')->
  info('  3.1 - Activation link')->

  get('/register/activation/'.$activationTokenUser1)->
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

$user = Doctrine::getTable('sfGuardUser')->findOneByUsername('user1@findeal.com');
$t->comment('Email validation: verify that the user changed group');
$user->refresh(true);
$t->is($user->getUsername(), 'user1@findeal.com',"::getUsername".$user->getId());
$t->is($user->getIsActive(), 1,"::getIsActive");

$groups = $user->getGroups();
if ($t->is($groups->count(), 1,"::getGroups count")){
  $t->is($groups[0]->getName(),'Customers',"::getName Customers");
}
else
{
  $t->fail('User without a group');
}

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

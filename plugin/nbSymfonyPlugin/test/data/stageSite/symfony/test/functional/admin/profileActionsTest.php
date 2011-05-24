<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
$t = new lime_test(1);

$browser = new FindealTestFunctional(new sfBrowser());
$browser->loadData();
$userId = 4;

$browser->info('1 - Login in module Profile')->
  get('/profile')->
  with('request')->begin()->
    isParameter('module', 'profile')->
    isParameter('action', 'index')->
  end()->
  click('Signin', array('signin' => array(
      'username'   => 'dealer1@example.com',
      'password' => 'dealer1pwd'
    )))->
  with('response')->
    isRedirected()->
    followRedirect()->
  with('request')->begin()->
    isParameter('module', 'profile')->
    isParameter('action', 'index')->
  end()
;

$browser->info('  1.2 - View user profile')->
  get('/profile')->

  with('request')->begin()->
    isParameter('module', 'profile')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end()
;

$browser->info('2 - Edit user profile')->
  info('  2.1 - Edit valid user profile')->
  get('/profile/edit')->
  with('request')->begin()->
    isParameter('module', 'profile')->
    isParameter('action', 'edit')->
  end()->
  click('Save', array('sf_guard_user_profile' => array(
      'birthday'     => '1984-02-01',
      'address'      => 'street, 1',
      'zip_code'     => '09123',
      'country'      => 'country',
      'nationality'  => 'Italy',
      'phone_number' => '070258963',
      'fax_number'   => '070147562',
      'website'      => 'www.example.com',
      'validate'     => 1
        )))->
  with('request')->begin()->
    isParameter('module', 'profile')->
    isParameter('action', 'update')->
  end()
;

$user = $browser->getUserByUserId($userId);
$t->is($user->getAddress(), 'street, 1', 'The User \'dealer1@example.com\' is modified');

$browser->info('3 - Validators')->
  info('  3.1 - Required fields')->
  get('/profile/edit')->
  click('Save', array('sf_guard_user_profile' => array(
      'birthday'     => '',
      'address'      => '',
      'zip_code'     => '',
      'country'      => '',
      'nationality'  => '',
      'phone_number' => '',
      'fax_number'   => '',
      'website'      => ''
  )))->

  with('form')->begin()->
    hasErrors(7)->
    isError('birthday', 'required')->
    isError('address', 'required')->
    isError('zip_code', 'required')->
    isError('country', 'required')->
    isError('nationality', 'required')->
    isError('phone_number', 'required')->
    isError('website', 'required')->
    //isError('email', 'invalid')->
  end()
;
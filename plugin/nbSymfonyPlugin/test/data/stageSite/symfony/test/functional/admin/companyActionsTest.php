<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');
$t = new lime_test(10);

$browser = new FindealTestFunctional(new sfBrowser());
$browser->loadData();

$browser->info('1 - Login in module Company')->
  get('/company')->
  with('request')->begin()->
    isParameter('module', 'company')->
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
    isParameter('module', 'company')->
    isParameter('action', 'index')->
  end()
;

$browser->info('2 - Create Company')->
  info('  2.1 - All fields are valid')->
  get('/company/new')->
  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('company' => array(
        'name'          => 'aCompany',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'phone_number'  => '070123456',
        'mobile_number' => '+393201234567',
        'email'         => 'acompany@exmaple.com',
        'website'       => 'http://www.acompany.com',
        )))->

  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aCompany'), true, 'The company \'aCompany\'is created');

$browser->info('  2.2 - Email field are not valid')->
  get('/company/new')->
  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('company' => array(
        'name'          => 'aWrongCompany',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'phone_number'  => '070123456',
        'mobile_number' => '+393201234567',
        'email'         => 'awrongcompany@exmaple',
        'website'       => 'http://www.awrongcompany.com',
        )))->

  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aWrongCompany'), false, 'The company \'aWrongCompany\'is not created');

$browser->info('  2.3 - At least one phone number is required')->
  info('    2.3.1 - With phone number - Without mobile number')->
  get('/company/new')->
  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('company' => array(
        'name'          => 'aCompanyWithPhoneNumber',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'phone_number'  => '070123456',
        'email'         => 'acompanywithphonenumber@exmaple.com',
        'website'       => 'http://www.acompanywithphonenumber.com',
        )))->

  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aCompanyWithPhoneNumber'), true, 'The company \'aCompanyWithPhoneNumber\'is created');

$browser->info('    2.3.2 - Without phone number - With mobile number')->
  get('/company/new')->
  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('company' => array(
        'name'          => 'aCompanyWithMobileNumber',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'mobile_number'  => '+393209237746',
        'email'         => 'acompanywithmobilenumber@exmaple.com',
        'website'       => 'http://www.acompanywithmobilenumber.com',
        )))->

  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aCompanyWithMobileNumber'), true, 'The company \'aCompanyWithMobileNumber\'is created');

$browser->info('    2.3.3 - Without phone number - Without mobile number')->
  get('/company/new')->
  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('company' => array(
        'name'          => 'aCompanyWithNoNumber',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'email'         => 'acompanywithnonumber@exmaple.com',
        'website'       => 'http://www.acompanywithnonumber.com',
        )))->

  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aCompanyWithNoNumber'), false, 'The company \'aCompanyWithNoNumber\'is not created');

$browser->info('    2.3.4 - With phone number - With mobile number')->
  get('/company/new')->
  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('company' => array(
        'name'          => 'aCompanyWithAllNumber',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'phone_number'  => '070234567',
        'mobile_number'  => '+393209237746',
        'email'         => 'acompanywithallnumber@exmaple.com',
        'website'       => 'http://www.acompanywithallnumber.com',
        )))->

  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aCompanyWithAllNumber'), true, 'The company \'aCompanyWithAllNumber\'is created');

$browser->info('    2.3.5 - With phone number equal to mobile number')->
  get('/company/new')->
  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('company' => array(
        'name'          => 'aCompanyWithIdenticalNumbers',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'phone_number'  => '+393209237746',
        'mobile_number'  => '+393209237746',
        'email'         => 'acompanywithidenticalnumbers@exmaple.com',
        'website'       => 'http://www.acompanywithidenticalnumbers.com',
        )))->

  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aCompanyWithIdenticalNumbers'), false, 'The company \'aCompanyWithIdenticalNumbers\'is not created');

$browser->info('  2.4 - Website field are not valid')->
  get('/company/new')->
      click('Save', array('company' => array(
        'name'          => 'aCompanyWrongWebsite',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'phone_number'  => '+393209237746',
        'mobile_number'  => '+393209237746',
        'email'         => 'acompanywrongwebsite@exmaple.com',
        'website'       => 'acompanywrongwebsitecom',
        )))->

  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aCompanyWrongWebsite'), false, 'The company \'aCompanyWrongWebsite\'is not created');

$browser->info('3 - Categories checkboxes')->
  info('  3.1 - Select one category')->
  get('/company/new')->
      click('Save', array('company' => array(
        'name'          => 'aCompanyWithCategory',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'phone_number'  => '+393209237746',
        'mobile_number' => '+393209237747',
        'email'         => 'aCompanyWithCategory@exmaple.com',
        'website'       => 'http://aCompanyWithCategory.com',
        'categories_list' => array(1)
        )))->
  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aCompanyWithCategory'), true, 'The company \'aCompanyWithCategory\' has a category');

$browser->info('  3.2 - Select two category')->
  get('/company/new')->
      click('Save', array('company' => array(
        'name'          => 'aCompanyWithTwoCategory',
        'description'   => 'aDescription',
        'partita_iva'   => '12345678901',
        'city'          => 'aCity',
        'address'       => 'anAddress',
        'state'         => 'Itlay',
        'zipcode'       => '09100',
        'phone_number'  => '+393209237746',
        'mobile_number' => '+393209237747',
        'email'         => 'aCompanyWithTwoCategory@exmaple.com',
        'website'       => 'http://aCompanyWithTwoCategory.com',
        'categories_list' => array(1,2)
        )))->
  with('request')->begin()->
    isParameter('module', 'company')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasCompanyByName('aCompanyWithCategory'), true, 'The company \'aCompanyWithTwoCategory\' has a category');

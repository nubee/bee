<?php
include(dirname(__FILE__).'/../../bootstrap/functional.php');
$t = new lime_test(6);

$browser = new FindealTestFunctional(new sfBrowser());
$browser->loadData();

$browser->info('1 - Login')->
  get('/deal')->
  with('request')->begin()->
    isParameter('module', 'deal')->
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
    isParameter('module', 'deal')->
    isParameter('action', 'index')->
  end()
;

$expiredDeals = 2;
$expiringDeals = 2;
$currentDeals = 3;

$browser->info('2 - List Deal')->
  info('  2.1 - Show all deals')->
  get('/deal')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    checkElement('tbody tr', $expiredDeals+$currentDeals )->
  end()
;

$browser->
  info('  2.2 - Show current deals')->
  get('/deal/current')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'current')->
  end()->
  with('response')->begin()->
    checkElement('tbody tr', $currentDeals)->
  end()
;

$browser->
  info('  2.3 - Show expiring deals')->
  get('/deal/expiring')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'expiring')->
  end()->
  with('response')->begin()->
    checkElement('tbody tr', $expiringDeals)->
  end()
;

$browser->
  info('  2.4 - Show expired deals')->
  get('/deal/expired')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'expired')->
  end()->
  with('response')->begin()->
    checkElement('tbody tr', $expiredDeals)->
  end()
;

$publishedAt = date('Y-m-d H:i:s', time());
$startAt = date('Y-m-d H:i:s', time() + 3600);
$endAt = date('Y-m-d H:i:s', time() + 7200);

$browser->info('3 - Create Deal')->
  info('  3.1 - All fields are valid')->
  get('/deal/new')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('deal' => array(
          'company_id'   => 1,
          'deal_type_id' => 1,
          'name'         => 'validDeal',
          'description'  => 'lastminute deal',
          'price'        => 99.99,
          'discount'     => 20,
          'published_at' => $publishedAt,
          'start_at'     => $startAt,
          'end_at'       => $endAt
        )))->

  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'create')->
  end()
;

$t->is($browser->hasDealByName('validDeal'), true, 'The deal \'validDeal\'is created');

$startAt = date('Y-m-d H:i:s', time() + 7200);
$endAt = date('Y-m-d H:i:s', time() + 3600);

$browser->info('  3.2 - startAt upper than endAt')->
  get('/deal/new')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('deal' => array(
          'company_id'   => 1,
          'deal_type_id' => 1,
          'name'         => 'startAt upper than endAt',
          'description'  => 'lastminute deal',
          'price'        => 99.99,
          'discount'     => 20,
          'start_at'     => $startAt,
          'end_at'       => $endAt
        )))->

  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasDealByName('startAt upper than endAt'), false, 'The deal \'startAt upper than endAt\' is not created');

$startdAt = date('Y-m-d H:i:s', time() + 3600);
$endAt = $startdAt;

$browser->info('  3.3 - startAt equal than endAt')->
  get('/deal/new')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('deal' => array(
          'company_id'   => 1,
          'deal_type_id' => 1,
          'name'         => 'startAt equal than endAt',
          'description'  => 'lastminute deal',
          'price'        => 100,
          'discount'     => 20,
          'start_at'     => $startAt,
          'end_at'       => $endAt
        )))->

  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasDealByName('startAt equal than endAt'), false, 'The deal \'startAt equal than endAt\' is not created');

$startAt = date('Y-m-d H:i:s', time());
$publishedAt = date('Y-m-d H:i:s', time() + 7200);
$endAt = date('Y-m-d H:i:s', time() + 7200);

$browser->info('  3.4 - publishedAt upper than startAt')->
  get('/deal/new')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('deal' => array(
          'company_id'   => 1,
          'deal_type_id' => 1,
          'name'         => 'publishedAt upper than startAt',
          'description'  => 'lastminute deal',
          'price'        => 100,
          'discount'     => 20,
          'published_at' => $publishedAt,
          'start_at'     => $startAt,
          'end_at'       => $endAt
        )))->

  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasDealByName('publishedAt upper than startAt'), false, 'The deal \'publishedAt upper than startAt\' is not created');

$startAt = date('Y-m-d H:i:s', time() + 3600);
$endAt = date('Y-m-d H:i:s', time() + 7200);

$browser->info('  3.5 - price upper than discount')->
  get('/deal/new')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('deal' => array(
          'company_id'   => 1,
          'deal_type_id' => 1,
          'name'         => 'price upper than discount',
          'description'  => 'lastminute deal',
          'price'        => 100,
          'discount'     => 150,
          'start_at'     => $startAt,
          'end_at'       => $endAt
        )))->

  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasDealByName('price upper than discount'), false, 'The deal \'price upper than discount\' is not created');

$browser->info('  3.6 - price equal than discount')->
  get('/deal/new')->
  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'new')->
  end()->
      click('Save', array('deal' => array(
          'company_id'   => 1,
          'deal_type_id' => 1,
          'name'         => ' price equal than discount',
          'description'  => 'lastminute deal',
          'price'        => 100,
          'discount'     => 100,
          'start_at'     => $startAt,
          'end_at'       => $endAt
        )))->

  with('request')->begin()->
    isParameter('module', 'deal')->
    isParameter('action', 'create')->
  end()
;
$t->is($browser->hasDealByName('price equal than discount'), false, 'The deal \' price equal than discount\' is not created');
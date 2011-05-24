<?php

include(dirname(__FILE__) . '/../../bootstrap/Doctrine.php');
$totalDeals = 8;
$currentDeals = 5;
$expiredDeals = 3;
$expiringDeals = 3;

$dealerId = 4;
$dealerTotalDeals = 5;
$dealerCurrentDeals = 3;
$dealerExpiredDeals = 2;
$dealerExpiringDeals = 2;

$t = new lime_test(44);

$t->comment('->getDeals()');
$deals = Doctrine_Core::getTable('Deal')->getDeals();
$t->is($deals->count(), $totalDeals, 'Number of total deals');

$t->comment('->getDeals($userId)');
$deals = Doctrine_Core::getTable('Deal')->getDeals($dealerId);
$t->is($deals->count(), $dealerTotalDeals, 'Number of dealer\'s deals');

$t->comment('->getCurrentDeals()');
$deals = Doctrine_Core::getTable('Deal')->getCurrentDeals();
$t->is($deals->count(), $currentDeals, 'Number of current deals');

$t->comment('->getCurrentDeals($userId)');
$deals = Doctrine_Core::getTable('Deal')->getCurrentDeals($dealerId);
$t->is($deals->count(), $dealerCurrentDeals, 'Number of dealer\'s current deals');

$t->comment('->getExpiringDeals()');
$deals = Doctrine_Core::getTable('Deal')->getExpiringDeals();
// test that deals are ordered by ascending expiring times within 2 hours
$t->is($deals->count(), $expiringDeals, 'Number of expiring deals within 2 hours');
for($i = 0; $i < $expiringDeals; $i++)
{
  $t->cmp_ok($deals[$i]->getEndAt(), '>', date('Y-m-d H:i:s', strtotime('-2 hour')), $deals[$i]->getName().' expire within 2 hours');
  if(isset($deals[$i + 1]))
    $t->cmp_ok($deals[$i]->getEndAt(), '<=', $deals[$i + 1]->getEndAt(), $deals[$i]->getName().' is later than '.$deals[$i + 1]->getName());
}

$t->comment('->getExpiringDeals($userId)');
$deals = Doctrine_Core::getTable('Deal')->getExpiringDeals($dealerId);
// test that deals are ordered by ascending expiring times within 2 hours
$t->is($deals->count(), $dealerExpiringDeals, 'Number of dealer\'s expiring deals within 2 hours');
for($i = 0; $i < $dealerExpiringDeals; $i++)
{
  $t->cmp_ok($deals[$i]->getEndAt(), '>', date('Y-m-d H:i:s', strtotime('-2 hour')), $deals[$i]->getName().' expire within 2 hours');
  if(isset($deals[$i + 1]))
    $t->cmp_ok($deals[$i]->getEndAt(), '<=', $deals[$i + 1]->getEndAt(), $deals[$i]->getName().' is later than '.$deals[$i + 1]->getName());
}

$t->comment('->getExpiredDeals()');
$deals = Doctrine_Core::getTable('Deal')->getExpiredDeals();
$t->is($deals->count(), $expiredDeals, 'Number of expired deals');
for($i = 0; $i < $expiredDeals; $i++)
{
  $t->cmp_ok($deals[$i]->getEndAt(), '<', date('Y-m-d H:i:s', time()), $deals[$i]->getName().' is expired');
}

$t->comment('->getExpiredDeals($userId)');
$deals = Doctrine_Core::getTable('Deal')->getExpiredDeals($dealerId);
$t->is($deals->count(), $dealerExpiredDeals, 'Number of dealer\'s expired deals');
for($i = 0; $i < $dealerExpiredDeals; $i++)
{
  $t->cmp_ok($deals[$i]->getEndAt(), '<', date('Y-m-d H:i:s', time()), $deals[$i]->getName().' is expired');
}

$t->comment('->getLatestDeals()');
$deals = Doctrine_Core::getTable('Deal')->getLatestDeals();
// test that deals are ordered by descending published times
$t->is($deals->count(), $currentDeals, 'Number of active deals');
for($i = 0; $i < $currentDeals - 1; $i++)
{
  $t->cmp_ok($deals[$i]->getPublishedAt(), '>', $deals[$i + 1]->getPublishedAt(), $deals[$i]->getName().' is later than '.$deals[$i + 1]->getName());
}

$t->comment('->getTopDiscountDeals()');
$deals = Doctrine_Core::getTable('Deal')->getTopDiscountDeals();
// test that deals are ordered by descending discount
$t->is($deals->count(), $currentDeals, 'Number of active deals');
for($i = 0; $i < $currentDeals - 1; $i++)
{
  $t->cmp_ok($deals[$i]->getDiscount(), '>', $deals[$i + 1]->getDiscount(), $deals[$i]->getName().' discount is greater than '.$deals[$i + 1]->getName());
}

$t->comment('Add a deal');
$publishedAt = date('Y-m-d H:i:s', time());
$startAt = date('Y-m-d H:i:s', time() + 3600);
$endAt = date('Y-m-d H:i:s', time() + 7200);

$aDeal = array(
  'company_id'   => 1,
  'deal_type_id' => 1, 
  'name'         => 'A deal',
  'description'  => 'incredible deal',
  'price'        => 99.99,
  'discount'     => 20,
  'published_at' => $publishedAt,
  'start_at'     => $startAt,
  'end_at'       => $endAt,
);

$deal = add_deal($aDeal);
$deal->save();
$t->pass('Deal inserted with id '.$deal->getId());
$t->is($deal->getCompanyId(),1,"::getCompanyId");
$t->is($deal->getDealTypeId(),1,"::getDealTypeId");
$t->is($deal->getName(),"A deal","::getName");
$t->is($deal->getDescription(),"incredible deal","::getDescription");
$t->is($deal->getPrice(),99.99,"::getPrice");
$t->is($deal->getDiscount(),20,"::getDiscount");
$t->is($deal->getPublishedAt(),$publishedAt,"::getPublishedAt");
$t->is($deal->getStartAt(),$startAt,"::getStartAt");
$t->is($deal->getEndAt(),$endAt,"::getEndAt");

$t->comment('Add a deal without published timestamp');

$aDeal = array(
  'company_id'   => 1,
  'deal_type_id' => 1,
  'name'         => 'Another deal',
  'description'  => 'another incredible deal',
  'price'        => 99.98,
  'discount'     => 10,
  'start_at'     => $startAt,
  'end_at'       => $endAt,
);
$deal = add_deal($aDeal);
$prePublishedAt = date('Y-m-d H:i:s', time());
$deal->save();
$postPublishedAt = date('Y-m-d H:i:s', time());
$t->pass('Deal inserted with id '.$deal->getId());
$t->ok($deal->getPublishedAt() >= $prePublishedAt,"::getPublishedAt");
$t->ok($deal->getPublishedAt() <= $postPublishedAt,"::getPublishedAt");

function add_deal($defaults = array()) {

  $deal = new Deal();
  $deal->fromArray(array_merge(array(
              'name' => 'Default name',
              'description' => 'Default description',
                  ), $defaults));

  return $deal;
}
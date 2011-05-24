<?php

include(dirname(__FILE__) . '/../../bootstrap/Doctrine.php');

$t = new lime_test(6);

$c1 = CompanyTable::getInstance()->findOneByName('FirstCompany');
$c2 = CompanyTable::getInstance()->findOneByName('ThreeCompany');

$t->comment('->hasLogo()');
$t->is($c1->hasLogo(), true, 'FirstCompany logo exists');
$t->is($c2->hasLogo(), false, 'ThreeCompany logo not exists');

$t->comment('->hasLogo()');
$t->is($c1->hasDeals(), true, 'FirstCompany has deals');
$t->is($c2->hasDeals(), false, 'ThreeCompany has not deals');

$t->comment('->getDealsByUserId($user_id)');
$deals = CompanyTable::getInstance()->getDealsByUserId(4);
$t->is($deals->count(), 5, 'User with id = 4 has 5 deals');
$deals = CompanyTable::getInstance()->getDealsByUserId(3);
$t->is($deals->count(), 0, 'User with id = 3 has not deals');


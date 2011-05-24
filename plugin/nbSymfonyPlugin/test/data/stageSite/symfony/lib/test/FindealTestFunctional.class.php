<?php
class FindealTestFunctional extends sfTestFunctional
{
  public function loadData()
  {
    Doctrine_Core::loadData(sfConfig::get('sf_test_dir').'/fixtures');
    return $this;
  }

  public function hasCompanyByName($companyName)
  {
    if(Doctrine_Core::getTable('Company')->findOneByName($companyName))
       return true;
    return false;
  }

  public function hasDealByName($dealName)
  {
    if(Doctrine_Core::getTable('Deal')->findOneByName($dealName))
       return true;
    return false;
  }

  public function getUserByUserId($userId)
  {
    $user = sfGuardUserProfileTable::getInstance()->findOneByUserId($userId);
    return $user;
  }

}

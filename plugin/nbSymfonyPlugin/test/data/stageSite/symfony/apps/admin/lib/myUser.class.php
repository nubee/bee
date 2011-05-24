<?php

class myUser extends sfGuardSecurityUser
{
  public function getCompanies()
  {
    return $this->getGuardUser()->getCompanies();
  }

  public function getDeals()
  {
    return CompanyTable::getInstance()->getDealsByUserId($this->getGuardUser()->getId());
  }

  public function getCurrentDeals()
  {
    return DealTable::getInstance()->getCurrentDeals($this->getGuardUser()->getId());
  }

  public function getExpiringDeals()
  {
    return DealTable::getInstance()->getExpiringDeals($this->getGuardUser()->getId());
  }

  public function getExpiredDeals()
  {
    return DealTable::getInstance()->getExpiredDeals($this->getGuardUser()->getId());
  }
}


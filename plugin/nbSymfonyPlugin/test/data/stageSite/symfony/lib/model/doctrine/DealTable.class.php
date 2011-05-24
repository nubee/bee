<?php

class DealTable extends Doctrine_Table
{
  public static function getInstance()
  {
    return Doctrine_Core::getTable('Deal');
  }

  private function getDealsQuery($userId = null)
  {
    $q = self::getInstance()->createQuery('d')
        ->leftJoin('d.Company c')
        ->leftJoin('d.DealType t')
        ;
    if($userId != null)
      $q->where('c.user_id = ?', $userId);

    return $q;
  }

  public function getDeals($userId = null)
  {
    $q = $this->getDealsQuery($userId)
        ->addOrderBy('d.published_at DESC')
        ;

    return $q->execute();
  }

  public function getCurrentDeals($userId = null)
  {
    $q = $this->getDealsQuery($userId)
      ->addWhere('d.end_at > ?', date('Y-m-d H:i:s', time()));
    return $q->execute();
  }

  public function getExpiringDeals($userId = null)
  {
    $q = $this->getDealsQuery($userId)
      ->addWhere('d.end_at > ?', date('Y-m-d H:i:s', time()))
      ->addWhere('d.end_at <= ?', date('Y-m-d H:i:s', strtotime('+2 hour')))
      ->addOrderBy('d.end_at');
    return $q->execute();
  }

  public function getExpiredDeals($userId = null)
  {
    $q = $this->getDealsQuery($userId)
        ->addWhere('d.end_at < ?', date('Y-m-d H:i:s', time()))
        ;
    return $q->execute();
  }

  public function getLatestDeals()
  {
    $q = $this->getDealsQuery()
      ->addWhere('d.end_at > ?', date('Y-m-d H:i:s', time()))
      ->addOrderBy('d.published_at DESC');
    return $q->execute();
  }

  public function getTopDiscountDeals()
  {
    $q = $this->getDealsQuery()
      ->addWhere('d.end_at > ?', date('Y-m-d H:i:s', time()))
      ->addOrderBy('d.discount DESC');
    return $q->execute();
  }
}
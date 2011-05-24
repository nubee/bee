<?php
class dealComponents extends sfComponents
{
  public function executeExpired()
  {
    $query = Doctrine::getTable('deal')
      ->createQuery()
      ->orderBy('end_at DESC')
      ->limit(5);
    $this->deals = $query->execute();
  }
}
